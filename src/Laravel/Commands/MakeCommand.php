<?php

namespace SaliBhdr\TyphoonTelegram\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class MakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new command class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Command';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/command.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Telegram\Commands';
    }

    /**
     * Build the class with the given name.
     * Remove the base controller import if we are already in base namespace.
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace["use {$controllerNamespace}\Command;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

}
