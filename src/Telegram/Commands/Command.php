<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Commands;

use Illuminate\Support\Str;
use SaliBhdr\TyphoonTelegram\Laravel\Facades\Telegram;
use SaliBhdr\TyphoonTelegram\Telegram\Api;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Message;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Update;

/**
 * Class Command.
 * @method mixed replyWithMessage($use_sendMessage_parameters)       Reply Chat with a message. You can use all the sendMessage() parameters except chat_id.
 * @method mixed replyWithPhoto($use_sendPhoto_parameters)           Reply Chat with a Photo. You can use all the sendPhoto() parameters except chat_id.
 * @method mixed replyWithAudio($use_sendAudio_parameters)           Reply Chat with an Audio message. You can use all the sendAudio() parameters except chat_id.
 * @method mixed replyWithVideo($use_sendVideo_parameters)           Reply Chat with a Video. You can use all the sendVideo() parameters except chat_id.
 * @method mixed replyWithVoice($use_sendVoice_parameters)           Reply Chat with a Voice message. You can use all the sendVoice() parameters except chat_id.
 * @method mixed replyWithDocument($use_sendDocument_parameters)     Reply Chat with a Document. You can use all the sendDocument() parameters except chat_id.
 * @method mixed replyWithSticker($use_sendSticker_parameters)       Reply Chat with a Sticker. You can use all the sendSticker() parameters except chat_id.
 * @method mixed replyWithLocation($use_sendLocation_parameters)     Reply Chat with a Location. You can use all the sendLocation() parameters except chat_id.
 * @method mixed replyWithChatAction($use_sendChatAction_parameters) Reply Chat with a Chat Action. You can use all the sendChatAction() parameters except chat_id.
 */
abstract class Command implements CommandInterface
{
    /**
     * make this true to not show in the list of help command
     * @var boolean
     */
    protected $hidden = false;

    /**
     * if true it handles command automatically and skip the config for handle_commands
     * @var bool
     */
    protected $handleAutomatically = true;

    /**
     * The name of the Telegram command.
     * Ex: help - Whenever the user sends /help, this would be resolved.
     * @var string
     */
    protected $name;

    /**
     * @var string The Telegram command description.
     */
    protected $description;

    /**
     * @var Api Holds the Super Class Instance.
     */
    protected $telegram;

    /**
     * @var string Arguments passed to the command.
     */
    protected $arguments;

    /**
     * @var Update Holds an Update object.
     */
    protected $update;

    /** @var Message */
    protected $message;

    public function __construct()
    {
        $this->name = fixCommandName($this->name);
    }

    /**
     * Get Command Name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Command Name.
     * @param $name
     * @return Command
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Command Description.
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Command Description.
     * @param $description
     * @return Command
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns Telegram Instance.
     * @return Api
     */
    public function getTelegram()
    {
        return $this->telegram;
    }

    /**
     * Returns Original Update.
     * @return Update
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Get Arguments passed to the command.
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @return bool
     */
    public function isHandleAutomatically(): bool
    {
        return $this->handleAutomatically;
    }

    /**
     * Returns an instance of Command Bus.
     * @return CommandBus
     */
    public function getCommandBus()
    {
        return $this->telegram->getCommandBus();
    }

    /**
     * {@inheritdoc}
     */
    public function make($telegram, $arguments, $update)
    {
        $this->telegram = $telegram;
        $this->arguments = $arguments;
        $this->update = $update;
        if ($this->update->has('callback_query')) {
            $this->message = $this->update
                ->getCallbackQuery()
                ->getMessage();
        }
        else {
            $this->message = $this->update->getMessage();
        }

        if (method_exists($this, 'boot'))
            $this->boot();

        return $this->handle($arguments);
    }

    /**
     * Helper to Trigger other Commands.
     * @param      $command
     * @param null $arguments
     * @return mixed
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException
     */
    protected function triggerCommand($command, $arguments = null)
    {
        return $this->getCommandBus()->execute($command, $arguments ?: $this->arguments, $this->update, false);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function handle($arguments);

    /**
     * Magic Method to handle all ReplyWith Methods.
     * @param $method
     * @param $arguments
     * @return mixed|string
     */
    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 9);
        if ($action === 'replyWith') {
            $reply_name = Str::studly(substr($method, 9));
            $methodName = 'send' . $reply_name;

            if (!method_exists($this->telegram, $methodName)) {
                return 'Method Not Found';
            }

            $chat_id = $this->update->getMessage()->getChat()->getId();
            $params = array_merge(compact('chat_id'), $arguments[0]);

            return call_user_func_array([$this->telegram, $methodName], [$params]);
        }

        return $this->$method(...$arguments);
    }

    /**
     * @param iterable $items
     * @param int      $columns
     * @param callable $callback
     * @return array
     */
    public function makeDynamicKeyboard(iterable $items, int $columns, callable $callback)
    {
        return Telegram::makeDynamicKeyboard($items, $columns, $callback);
    }
}
