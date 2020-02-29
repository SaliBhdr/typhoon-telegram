<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Commands;

use SaliBhdr\TyphoonTelegram\Telegram\Api;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramCommandNotFoundException;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Update;

/**
 * Class CommandBus.
 */
class CommandBus
{
    protected $isCommandHandled = false;

    /**
     * @var Command[] Holds all commands.
     */
    protected $commands = [];

    /**
     * @var Api
     */
    private $telegram;


    /**
     * Instantiate Command Bus.
     *
     * @param Api $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Returns the list of commands.
     *
     * @param bool $hidden
     *
     * @return array
     */
    public function getCommands(bool $hidden = true)
    {
        if (!$hidden) {
            $commands = array_filter($this->commands, function(Command $command) {
                return !$command->isHidden();
            });

            return $commands;
        }

        return $this->commands;
    }

    /**
     * Add a list of commands.
     *
     * @param array $commands
     *
     * @return CommandBus
     * @throws TelegramCommandNotFoundException
     * @throws TelegramException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function addCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }

        return $this;
    }

    /**
     * Add a command to the commands list.
     *
     * @param CommandInterface|string $command Either an object or full path to the command class.
     *
     * @return CommandBus
     * @throws TelegramCommandNotFoundException
     * @throws TelegramException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function addCommand($command)
    {
        if (!is_object($command)) {
            if (!class_exists($command)) {
                throw new TelegramCommandNotFoundException(
                    sprintf(
                        'Command class "%s" not found! Please make sure the class exists.',
                        $command
                    )
                );
            }

            if ($this->telegram->hasContainer()) {
                $command = $this->buildDependencyInjectedCommand($command);
            }
            else {
                $command = new $command();
            }
        }

        if ($command instanceof CommandInterface) {

            /*
             * At this stage we definitely have a proper command to use.
             *
             * @var Command $command
             */
            $this->commands[$command->getName()] = $command;

            return $this;
        }

        throw new TelegramException(
            sprintf(
                'Command class "%s" should be an instance of "SaliBhdr\TyphoonTelegram\Commands\CommandInterface"',
                get_class($command)
            )
        );
    }

    /**
     * Remove a command from the list.
     *
     * @param $name
     *
     * @return CommandBus
     */
    public function removeCommand($name)
    {
        unset($this->commands[$name]);

        return $this;
    }

    /**
     * Removes a list of commands.
     *
     * @param array $names
     *
     * @return CommandBus
     */
    public function removeCommands(array $names)
    {
        foreach ($names as $name) {
            $this->removeCommand($name);
        }

        return $this;
    }

    /**
     * Handles Inbound Messages and Executes Appropriate Command.
     *
     * @param        $command_name
     * @param Update $update
     * @param bool $fromApi
     * @param null $arguments
     *
     * @return Update
     * @throws TelegramException
     */
    public function handler($command_name, Update $update, bool $fromApi = false, $arguments = null)
    {
        $match = $this->parseCommand($command_name);

        if(is_array($match))
            $match = array_filter($match);

        if (!empty($match)) {
            $arguments = $match[3] ?? $arguments;

            return $this->execute($match[1], $arguments, $update, $fromApi);
        }
        else {
            return $this->execute($command_name, $arguments, $update, $fromApi);
        }
    }

    /**
     * Parse a Command for a Match.
     *
     * @param $text
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function parseCommand($text)
    {
        $text = fixCommandName($text);

        if (trim($text) === '') {
            throw new \InvalidArgumentException('Message is empty, Cannot parse for command');
        }

        preg_match('/^\/([^\s@]+)@?(\S+)?\s?(.*)$/', $text, $matches);

        return $matches;
    }

    /**
     * Execute the command.
     *
     * @param      $commandName
     * @param      $arguments
     * @param      $message
     * @param bool $fromApi
     *
     * @return mixed
     * @throws TelegramException
     */
    public function execute($commandName, $arguments, $message, bool $fromApi)
    {
        $commandName = fixCommandName($commandName);

        if (count(explode(' ', $commandName)) > 1)
            return false;

        if (array_key_exists($commandName, $this->commands)) {
            /** @var Command $command */
            $command = $this->commands[$commandName];

            $isRunnable = config('telegram.handle_commands');

            if ($fromApi)
                $isRunnable = $isRunnable && $command->isHandleAutomatically();

            if ($isRunnable) {
                $this->isCommandHandled = true;

                return $this->commands[$commandName]->make($this->telegram, $arguments, $message);
            }

            return false;
        }
        else {
            if (config('telegram.debug'))
                throw new TelegramException("Command {$commandName} not found!");

            return false;
        }
    }

    /**
     * Use PHP Reflection and Laravel Container to instantiate the command with type hinted dependencies.
     *
     * @param $commandClass
     *
     * @return \object
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    private function buildDependencyInjectedCommand($commandClass)
    {

        // check if the command has a constructor
        if (!method_exists($commandClass, '__construct')) {
            return new $commandClass();
        }

        // get constructor params
        $constructorReflector = new \ReflectionMethod($commandClass, '__construct');
        $params               = $constructorReflector->getParameters();

        // if no params are needed proceed with normal instantiation
        if (empty($params)) {
            return new $commandClass();
        }

        // otherwise fetch each dependency out of the container
        $container    = $this->telegram->getContainer();
        $dependencies = [];
        foreach ($params as $param) {
            $dependencies[] = $container->make($param->getClass()->name);
        }

        // and instantiate the object with dependencies through ReflectionClass
        $classReflector = new \ReflectionClass($commandClass);

        return $classReflector->newInstanceArgs($dependencies);
    }

    /**
     * @return bool
     */
    public function isCommandHandled() : bool
    {
        return $this->isCommandHandled;
    }
}
