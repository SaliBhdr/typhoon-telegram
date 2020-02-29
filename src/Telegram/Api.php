<?php

namespace SaliBhdr\TyphoonTelegram\Telegram;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use SaliBhdr\TyphoonTelegram\Telegram\Commands\CommandBus;
use SaliBhdr\TyphoonTelegram\Telegram\Commands\CommandInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramInvalidChatActionException;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramTokenNotProvidedException;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramWebhookException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\MethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods\SendDynamic;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Client as TelegramClient;
use SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\GuzzleHttpClient;
use SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\HttpClientInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Inputs\InputFile;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Request as TelegramRequest;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\BaseModel;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\BoolModel;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Chat;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\ChatAdministrators;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\ChatMember;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\File;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\MembersCount;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Message;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\ModelDecorator;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\TelegramCollection;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Update;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\User;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\UserProfilePhotos;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Response as TelegramResponse;

/*** Class Api.
 */
class Api
{
    /**
     * @var string Version number of the Telegram Bot PHP SDK.
     */
    const VERSION = '1.0.0';

    /**
     * @var string The name of the environment variable that contains the Telegram Bot API Access Token.
     */
    const BOT_TOKEN_ENV_NAME = 'TELEGRAM_DEFAULT_BOT_TOKEN';

    /**
     * @var TelegramClient The Telegram client service.
     */
    protected $client;

    /**
     * @var string Telegram Bot API Access Token.
     */
    protected $accessToken;

    /**
     * @var TelegramResponse|null Stores the last response of the request that made to Telegram Bot API.
     */
    protected $lastResponse;

    /**
     * @var TelegramRequest|null Stores the last request that made or going to be make to Telegram Bot API.
     */
    protected $lastRequest;

    /**
     * @var bool Indicates if the request to Telegram will be asynchronous (non-blocking).
     */
    protected $isAsyncRequest = false;

    /**
     * @var CommandBus|null Telegram Command Bus.
     */
    protected $commandBus;

    /**
     * @var Container IoC Container
     */
    protected static $container;

    /**
     * @var Api $instance
     */
    protected static $instance;

    /**
     * enables and disables checking token existence in every request
     *
     * @var bool $tokenCheck
     */
    protected $tokenCheck = true;

    /**
     * array of telegram available chat action
     *
     * @var array $chatActions
     */
    protected $chatActions = [
            'typing',
            'upload_photo',
            'record_video',
            'upload_video',
            'record_audio',
            'upload_audio',
            'upload_document',
            'find_location',
            'record_video_note',
            'upload_video_note',
        ];


    /**
     * Instantiates a new Telegram super-class object.
     *
     * @param string $token The Telegram Bot API Access Token.
     * @param bool $async (Optional) Indicates if the request to Telegram
     *                                                        will be asynchronous (non-blocking).
     * @param string|HttpClientInterface $http_client_handler (Optional) Custom HTTP Client Handler.
     */
    private function __construct(string $token = null, bool $async = false, $http_client_handler = null)
    {

        $this->setDefaultAccessToken($token);

        if (isset($async))
            $this->setAsyncRequest($async);


        $this->setHttpClientHandler($http_client_handler);
    }

    /**
     * sets the token of the bot named default
     *
     * @param $token
     */
    private function setDefaultAccessToken($token)
    {
        if (isset($token))
            $this->accessToken = $token;
        else
            $this->defaultBot();
    }

    protected function setHttpClientHandler($http_client_handler)
    {
        if (!isset($http_client_handler) || $http_client_handler === 'guzzle') {
            $http_client_handler = new GuzzleHttpClient();
        }
        else {
            $http_client_handler = new $http_client_handler();
        }

        if (!$http_client_handler instanceof HttpClientInterface) {
            throw new \InvalidArgumentException('The HTTP Client Handler must be set to "guzzle", or be an instance of ' . HttpClientInterface::class);
        }

        $this->client = new TelegramClient($http_client_handler);
    }

    /**
     * @param string|NULL $token
     * @param bool $async
     * @param null $http_client_handler
     *
     * @return Api
     */
    public static function init(string $token = null, bool $async = false, $http_client_handler = null)
    {
        if (empty(static::$instance))
            static::$instance = new static($token, $async, $http_client_handler);

        return static::$instance;
    }

    /**    * Handle dynamic static method calls into the method.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'bot')
            return (new static)->$method(...$parameters);

        return static::$method(...$parameters);
    }

    /**
     * @param $apiMethodObj
     *
     * @return mixed
     * @throws Exceptions\TelegramParamsRequiredException
     */

    public function send(MethodAbstract $apiMethodObj)
    {
        if ($apiMethodObj instanceof SendDynamic) {
            return new TelegramCollection($this->{$apiMethodObj->getRequestMethod()}($apiMethodObj->method(), $apiMethodObj->getParams()));
        }

        return $this->{$apiMethodObj->method()}($apiMethodObj->getParams());

    }

    /**
     * @param array $keyboard
     *
     * @return array
     */
    public function inlineKeyboardMarkup(array $keyboard)
    {
        if (!array_key_exists('inline_keyboard', $keyboard)) {
            return ['inline_keyboard' => $keyboard];
        }

        return $keyboard;
    }

    /**
     * Broadcast a Chat Action.
     * <code>
     * $params = [
     *   'chat_id' => '',
     *   'action'  => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendchataction
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['action']
     * @throws TelegramException
     * @return TelegramResponse
     * @throws TelegramInvalidChatActionException
     */
    public function sendChatAction(array $params)
    {
        if (isset($params['action']) && in_array($params['action'], $this->chatActions))
            return $this->post('sendChatAction', $params);

        throw new TelegramInvalidChatActionException($this->chatActions);
    }


    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionTyping($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);
    }

    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionRecordVideo($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'record_video']);
    }



    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionUploadPhoto($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_photo']);
    }

    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionUploadVideo($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_video']);
    }

    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionUploadAudio($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_audio']);
    }

    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionUploadVoice($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_audio']);
    }

    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionUploadDocument($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_document']);
    }

    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionRecordAudio($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'record_audio']);
    }


    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionFindLocation($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'find_location']);
    }


    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionRecordVideoNote($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'record_video_note']);
    }

    /**
     * Broadcast a Chat Action.
     *
     * @param int|string $chatId
     * @return TelegramResponse
     * @throws TelegramException
     * @throws TelegramInvalidChatActionException
     */
    public function sendActionUploadVideoNote($chatId)
    {
        return $this->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_video_note']);
    }

    /**
     * sets default bot to handle requests
     */
    public function defaultBot()
    {
        $this->bot(config('telegram.default_bot'));
    }

    /**
     * @param string $botName
     *
     * @return $this
     */
    public function bot(string $botName = null)
    {
        $bot = config('telegram.bots.' . $botName);

        if (!is_null($bot) && is_array($bot) && !empty($bot) && isset($bot['botToken']) && isset($bot['is_active']) && $bot['is_active']) {
            $this->token($bot['botToken']);
        }

        return $this;
    }

    /**
     * Sets the bot access token to use with API requests.
     *
     * @param string $botToken The bot access token to save.
     *
     * @return Api
     * @throws \InvalidArgumentException
     */
    public function token($botToken)
    {
        if (is_string($botToken)) {
            $this->accessToken = $botToken;

            return $this;
        }

        throw new \InvalidArgumentException('The Telegram bot access token must be of type "string"');
    }

    /**
     * Send general files.
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'document'            => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#senddocument
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['document']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message
     * @throws TelegramException
     */
    public function sendAnimation(array $params)
    {
        return $this->uploadFile('sendAnimation', $params);
    }

    public function sendVideoNote(array $params)
    {
        return $this->uploadFile('sendVideoNote', $params);
    }

    public function sendMediaGroup(array $params)
    {
        return $this->uploadFile('sendMediaGroup', $params);
    }

    public function editMessageLiveLocation(array $params)
    {
        return $this->post('editMessageLiveLocation', $params);
    }

    public function stopMessageLiveLocation(array $params)
    {
        return $this->post('stopMessageLiveLocation', $params);
    }

    public function sendVenue(array $params)
    {
        return $this->post('sendVenue', $params);
    }

    public function sendContact(array $params)
    {
        return $this->post('sendContact', $params);
    }

    public function sendPoll(array $params)
    {
        return $this->post('sendPoll', $params);
    }

    public function kickChatMember(array $params)
    {
        return $this->post('kickChatMember', $params);
    }

    public function unbanChatMember(array $params)
    {
        return $this->post('unbanChatMember', $params);
    }

    public function restrictChatMember(array $params)
    {
        return $this->post('restrictChatMember', $params);
    }

    public function promoteChatMember(array $params)
    {
        return $this->post('promoteChatMember', $params);
    }

    public function exportChatInviteLink(array $params)
    {
        return $this->post('exportChatInviteLink', $params);
    }

    public function setChatPhoto(array $params)
    {
        return $this->uploadFile('setChatPhoto', $params);
    }

    public function deleteChatPhoto(array $params)
    {
        return $this->post('deleteChatPhoto', $params);
    }

    public function setChatTitle(array $params)
    {
        return $this->post('setChatTitle', $params);
    }

    public function setChatDescription(array $params)
    {
        return $this->post('setChatDescription', $params);
    }

    public function pinChatMessage(array $params)
    {
        return $this->post('pinChatMessage', $params);
    }

    public function unpinChatMessage(array $params)
    {
        return $this->post('unpinChatMessage', $params);
    }

    public function leaveChat(array $params)
    {
        return $this->post('leaveChat', $params);
    }


    public function setChatStickerSet(array $params)
    {
        return $this->post('setChatStickerSet', $params);
    }



    public function sendEditMessageText(array $params)
    {
        return $this->post('editMessageText', $params);
    }

    /**
     **********************************************************************************/


    /**
     * Returns the Client service.
     * @return TelegramClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns Telegram Bot API Access Token.
     * @return string
     * @throws TelegramTokenNotProvidedException
     */
    public function getAccessToken()
    {
        if (!$this->accessToken && $this->tokenCheck)
            throw new TelegramTokenNotProvidedException();

        return $this->accessToken;
    }

    /**
     * Returns the last response returned from API request.
     * @return TelegramResponse
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }


    /**
     * Make this request asynchronous (non-blocking).
     *
     * @param bool $isAsyncRequest
     *
     * @return Api
     */
    public function setAsyncRequest($isAsyncRequest)
    {
        $this->isAsyncRequest = $isAsyncRequest;

        return $this;
    }

    /**
     * Returns SDK's Command Bus.
     *
     * @return CommandBus
     */
    public function getCommandBus()
    {
        if (is_null($this->commandBus)) {
            return $this->commandBus = new CommandBus($this);
        }

        return $this->commandBus;
    }

    /**
     * Add Telegram Command to the Command Bus.
     *
     * @param CommandInterface|string $command
     *
     * @return CommandBus
     * @throws Exceptions\TelegramCommandNotFoundException
     * @throws TelegramException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function addCommand($command)
    {
        return $this->getCommandBus()->addCommand($command);
    }

    /**
     * Add Telegram Commands to the Command Bus.
     *
     * @param array $commands
     *
     * @return CommandBus
     * @throws Exceptions\TelegramCommandNotFoundException
     * @throws TelegramException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function addCommands(array $commands)
    {
        return $this->getCommandBus()->addCommands($commands);
    }

    /**
     * Remove Telegram Command to the Command Bus.
     *
     * @param string $name
     *
     * @return CommandBus
     */
    public function removeCommand($name)
    {
        return $this->getCommandBus()->removeCommand($name);
    }

    /**
     * Remove Telegram Commands from the Command Bus.
     *
     * @param array $names
     *
     * @return CommandBus
     */
    public function removeCommands(array $names)
    {
        return $this->getCommandBus()->removeCommands($names);
    }

    /**
     * Returns list of available commands.
     *
     * @param bool $hidden
     *
     * @return Commands\Command[]
     */
    public function getCommands($hidden = true)
    {
        return $this->getCommandBus()->getCommands($hidden);
    }

    /**
     * A simple method for testing your bot's auth token.
     * Returns basic information about the bot in form of a User object.
     * @link https://core.telegram.org/bots/api#getme
     * @return User|BaseModel
     * @throws TelegramException
     */
    public function getMe()
    {
        $response = $this->post('getMe');

        return $this->makeUserCollection($response);
    }

    /**
     * Use this method to delete a message, including service messages, with the following limitations:
     * A message can only be deleted if it was sent less than 48 hours ago.
     * Bots can delete outgoing messages in private chats, groups, and supergroups.
     * Bots can delete incoming messages in private chats.
     * Bots granted can_post_messages permissions can delete outgoing messages in channels.
     * If the bot is an administrator of a group, it can delete any message there.
     * If the bot has can_delete_messages permission in a supergroup or a channel, it can delete any message there.
     * Returns True on success.
     * <code>
     * $params = [
     *   'chat_id'                  => '',
     *   'message_id'               => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#deletemessage
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var int $params ['message_id']
     * @return Message|BaseModel
     * @throws TelegramException
     */
    public function deleteMessage(array $params)
    {
        $response = $this->post('deleteMessage', $params);

        return $this->makeBoolResponseCollection($response);
    }

    /**
     * Use this method to send answers to callback queries sent from inline keyboards.
     * The answer will be displayed to the user as a notification at the top of the chat screen or as an alert.
     * On success, True is returned.
     *
     * <code>
     * $params = [
     *   'callback_query_id'    => '',
     *   'text'                 => '',
     *   'show_alert'           => false,
     *   'url'                  => '',
     *   'cache_time'           => 0,
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#answercallbackquery
     *
     * @param array $params
     *
     * @var string $params ['callback_query_id']
     * @var string $params ['text']
     * @var bool $params ['show_alert']
     * @var string $params ['url']
     * @var int $params ['cache_time']
     * @return Message|BaseModel
     * @throws TelegramException
     */
    public function answerCallbackQuery(array $params)
    {
        $response = $this->post('answerCallbackQuery', $params);

        return $this->makeBoolResponseCollection($response);
    }

    /**
     * Send text messages.
     * <code>
     * $params = [
     *   'chat_id'                  => '',
     *   'text'                     => '',
     *   'parse_mode'               => '',
     *   'disable_web_page_preview' => '',
     *   'reply_to_message_id'      => '',
     *   'reply_markup'             => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendmessage
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['text']
     * @var string $params ['parse_mode']
     * @var bool $params ['disable_web_page_preview']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message|BaseModel
     * @throws TelegramException
     */
    public function sendMessage(array $params)
    {
        $response = $this->post('sendMessage', $params);

        return $this->makeMessageCollection($response);
    }

    /**
     * get admins of a channel.
     * <code>
     * $params = [
     *   'chat_id'  => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#getchatadministrators
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     *
     * @return BaseModel|Message
     * @throws TelegramException
     */
    public function getChatAdministrators(array $params)
    {
        $response = $this->post('getChatAdministrators', $params);

        return $this->makeChatAdministratorsCollection($response);
    }

    /**
     * get members of a channel.
     * <code>
     * $params = [
     *   'chat_id'  => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#getchatmemberscount
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     *
     * @return BaseModel|Message
     * @throws TelegramException
     */
    public function getChatMembersCount(array $params)
    {
        $response = $this->post('getChatMembersCount', $params);

        return $this->makeChatMembersCountCollection($response);
    }

    /**
     * get a chat members of a channel.
     * <code>
     * $params = [
     *   'chat_id'  => '',
     *   'user_id'  => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#getchatmember
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var int $params ['user_id']
     *
     * @return mixed
     * @throws TelegramException
     */
    public function getChatMember(array $params)
    {
        $response = $this->post('getChatMember', $params);

        return $this->makeChatMemberCollection($response);
    }

    /**
     * get a chat members of a channel.
     * <code>
     * $params = [
     *   'chat_id'  => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#getchat
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     *
     * @return mixed
     * @throws TelegramException
     */
    public function getChat(array $params)
    {
        $response = $this->post('getChat', $params);

        return $this->makeChatCollection($response);
    }

    /**
     * Send text messages.
     * <code>
     * $params = [
     *   'chat_id'                  => '',
     *   'text'                     => '',
     *   'parse_mode'               => '',
     *   'disable_web_page_preview' => '',
     *   'reply_to_message_id'      => '',
     *   'reply_markup'             => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendmessage
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['text']
     * @var string $params ['parse_mode']
     * @var bool $params ['disable_web_page_preview']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message|BaseModel
     * @throws TelegramException
     */
    public function sendReplyMessage(array $params)
    {
        $response = $this->post('sendMessage', $params);

        return $this->makeMessageCollection($response);
    }

    /**
     * Forward messages of any kind.
     * <code>
     * $params = [
     *   'chat_id'      => '',
     *   'from_chat_id' => '',
     *   'message_id'   => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#forwardmessage
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var int $params ['from_chat_id']
     * @var int $params ['message_id']
     * @return Message|BaseModel
     * @throws TelegramException
     */
    public function forwardMessage(array $params)
    {
        $response = $this->post('forwardMessage', $params);

        return $this->makeMessageCollection($response);
    }

    /**
     * Send Photos.
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'photo'               => '',
     *   'caption'             => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendphoto
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['photo']
     * @var string $params ['caption']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message
     * @throws TelegramException
     */
    public function sendPhoto(array $params)
    {
        return $this->uploadFile('sendPhoto', $params);
    }

    /**
     * Send regular audio files.
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'audio'               => '',
     *   'duration'            => '',
     *   'performer'           => '',
     *   'title'               => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendaudio
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['audio']
     * @var int $params ['duration']
     * @var string $params ['performer']
     * @var string $params ['title']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message
     * @throws TelegramException
     */
    public function sendAudio(array $params)
    {
        return $this->uploadFile('sendAudio', $params);
    }

    /**
     * Send general files.
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'document'            => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#senddocument
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['document']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message
     * @throws TelegramException
     */
    public function sendDocument(array $params)
    {
        return $this->uploadFile('sendDocument', $params);
    }

    /**
     * Send .webp stickers.
     * <code>
     * $params = [
     *   'chat_id' => '',
     *   'sticker' => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup' => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendsticker
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['sticker']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @throws TelegramException
     * @return Message
     */
    public function sendSticker(array $params)
    {
        if (is_file($params['sticker']) && (pathinfo($params['sticker'], PATHINFO_EXTENSION) !== 'webp')) {
            throw new TelegramException('Invalid Sticker Provided. Supported Format: Webp');
        }

        return $this->uploadFile('sendSticker', $params);
    }

    /**
     * Send Video File, Telegram clients support mp4 videos (other formats may be sent as Document).
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'video'               => '',
     *   'duration'            => '',
     *   'caption'             => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     * @param array $params
     *
     * @return Message
     * @throws TelegramException
     * @see  sendDocument
     * @link https://core.telegram.org/bots/api#sendvideo
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['video']
     * @var int $params ['duration']
     * @var string $params ['caption']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     */
    public function sendVideo(array $params)
    {
        return $this->uploadFile('sendVideo', $params);
    }

    /**
     * Send voice audio files.
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'voice'               => '',
     *   'duration'            => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendaudio
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['voice']
     * @var int $params ['duration']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message
     * @throws TelegramException
     */
    public function sendVoice(array $params)
    {
        return $this->uploadFile('sendVoice', $params);
    }

    /**
     * Send point on the map.
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'latitude'            => '',
     *   'longitude'           => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#sendlocation
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var float $params ['latitude']
     * @var float $params ['longitude']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     * @return Message|BaseModel
     * @throws TelegramException
     */
    public function sendLocation(array $params)
    {
        $response = $this->post('sendLocation', $params);

        return $this->makeMessageCollection($response);
    }


    /**
     * Returns a list of profile pictures for a user.
     * <code>
     * $params = [
     *   'user_id' => '',
     *   'offset'  => '',
     *   'limit'   => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#getuserprofilephotos
     *
     * @param array $params
     *
     * @var int $params ['user_id']
     * @var int $params ['offset']
     * @var int $params ['limit']
     * @return UserProfilePhotos|BaseModel
     * @throws TelegramException
     */
    public function getUserProfilePhotos(array $params)
    {
        $response = $this->post('getUserProfilePhotos', $params);

        return $this->makeUserProfilePhotosCollection($response);
    }

    /**
     * Returns basic info about a file and prepare it for downloading.
     * <code>
     * $params = [
     *   'file_id' => '',
     * ];
     * </code>
     * The file can then be downloaded via the link
     * https://api.telegram.org/file/bot<token>/<file_path>,
     * where <file_path> is taken from the response.
     * @link https://core.telegram.org/bots/api#getFile
     *
     * @param array $params
     *
     * @var string $params ['file_id']
     * @return File|BaseModel
     * @throws TelegramException
     */
    public function getFile(array $params)
    {
        $response = $this->post('getFile', $params);

        return $this->makeFileCollection($response);
    }

    /**
     * Set a Webhook to receive incoming updates via an outgoing webhook.
     * <code>
     * $params = [
     *   'url'         => '',
     *   'certificate' => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#setwebhook
     *
     * @param array $params
     *
     * @var string $params ['url']         HTTPS url to send updates to.
     * @var string $params ['certificate'] Upload your public key certificate so that the root certificate in
     *                                      use can be checked.
     * @return Message
     * @throws TelegramException
     */
    public function setWebhook(array $params)
    {
        if (filter_var($params['url'], FILTER_VALIDATE_URL) === false) {
            throw new TelegramWebhookException('Invalid URL, Provided url has invalid format');
        }

        if (parse_url($params['url'], PHP_URL_SCHEME) !== 'https') {
            throw new TelegramWebhookException('Invalid URL, The url must br start with HTTPS');
        }

        return $this->uploadFile('setWebhook', $params);
    }

    /**
     * Returns webhook updates sent by Telegram.
     * Works only if you set a webhook.
     * @return Update
     * @see setWebhook
     */
    public function getWebhookUpdates()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        return new Update($body);
    }

    /**
     * Removes the outgoing webhook (if any).
     * @return TelegramResponse
     * @throws TelegramException
     */
    public function removeWebhook()
    {
        $url = '';

        return $this->post('setWebhook', compact('url'));
    }

    /**
     * Use this method to receive incoming updates using long polling.
     * <code>
     * $params = [
     *   'offset'  => '',
     *   'limit'   => '',
     *   'timeout' => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#getupdates
     *
     * @param array $params
     *
     * @var int|null $params ['offset']
     * @var int|null $params ['limit']
     * @var int|null $params ['timeout']
     * @return Update[]
     * @throws TelegramException
     */
    public function getUpdates(array $params = [])
    {
        $response = $this->post('getUpdates', $params);
        $updates  = $response->getDecodedBody();

        $data = [];
        if (isset($updates['result'])) {
            foreach ($updates['result'] as $update) {
                $data[] = new Update($update);
            }
        }

        return $data;
    }

    /**
     * Builds a custom keyboard markup.
     * <code>
     * $params = [
     *   'keyboard'          => '',
     *   'resize_keyboard'   => '',
     *   'one_time_keyboard' => '',
     *   'selective'         => '',
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#replykeyboardmarkup
     *
     * @param array $params
     *
     * @var array $params ['keyboard']
     * @var bool $params ['resize_keyboard']
     * @var bool $params ['one_time_keyboard']
     * @var bool $params ['selective']
     * @return string
     */
    public function replyKeyboardMarkup(array $params)
    {
        return json_encode($params);
    }

    /**
     * make dynamically keyboard form an iterable (array, Collection)
     *
     * callback map $callback($index,$item)
     *
     * @param iterable $items
     * @param int $columns
     * @param callable $callback
     * @return array
     */
    public function makeDynamicKeyboard(iterable $items, int $columns, callable $callback)
    {
        $columns  = $columns - 1;
        $row      = 0;
        $column   = 0;
        $keyboard = [];
        foreach ($items as $index => $item) {

            $button = $callback($index, $item);

            if (!is_null($button)) {
                $keyboard[$row][$column] = is_array($button) ? $button : [$button];

                if ($column - $columns == 0) {
                    $column = 0;
                    $row++;
                }
                else
                    $column++;

            }

        }

        return $keyboard;
    }

    /**
     * Hide the current custom keyboard and display the default letter-keyboard.
     * <code>
     * $params = [
     *   'hide_keyboard' => true,
     *   'selective'     => false,
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#replykeyboardhide
     *
     * @param array $params
     *
     * @var bool $params ['hide_keyboard']
     * @var bool $params ['selective']
     * @return string
     */
    public static function replyKeyboardHide(array $params = [])
    {
        return json_encode(array_merge(['hide_keyboard' => true, 'selective' => false], $params));
    }


    /**
     * Display a reply interface to the user (act as if the user has selected the bot‘s message and tapped ’Reply').
     * <code>
     * $params = [
     *   'force_reply' => true,
     *   'selective'   => false,
     * ];
     * </code>
     * @link https://core.telegram.org/bots/api#forcereply
     *
     * @param array $params
     *
     * @var bool $params ['force_reply']
     * @var bool $params ['selective']
     * @return string
     */
    public static function forceReply(array $params = [])
    {
        return json_encode(array_merge(['force_reply' => true, 'selective' => false], $params));
    }

    /**
     * Processes Inbound Commands.
     *
     * @param bool $webhook
     *
     * @return Update|Update[]
     * @throws TelegramException
     */
    public function handleBot($webhook = false)
    {
        $updateProcessor = new UpdateProcessor();

        if ($webhook)
            return $updateProcessor->processViaWebhook();

        return $updateProcessor->processWithoutWebhook();
    }

    /**
     * @param $command
     *
     * @param null $arguments
     *
     * @return Update
     * @throws TelegramException
     */
    public function command($command, $arguments = null)
    {
        if (strpos($command, "/") === 0)
            $command = '/' . $command;

        $update = $this->getWebhookUpdates();

        return $this->getCommandBus()->handler($command, $update, false, $arguments);
    }

    /**
     * Determine if a given type is the message.
     *
     * @param string $type
     * @param Update|Message $object
     *
     * @return bool
     */
    public function isMessageType($type, $object)
    {
        if ($object instanceof Update) {
            $object = $object->getMessage();
        }

        if ($object->has(strtolower($type))) {
            return true;
        }

        return $this->detectMessageType($object) === $type;
    }

    /**
     * Detect Message Type Based on Update or Message Object.
     *
     * @param Update|Message $object
     *
     * @return string|null
     */
    public function detectMessageType($object)
    {
        if ($object instanceof Update) {
            $object = $object->getMessage();
        }

        $types = ['audio', 'document', 'photo', 'sticker', 'video', 'voice', 'contact', 'location', 'text'];

        return $object->keys()->intersect($types)->pop();
    }

    /**
     * @return null|TelegramRequest
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return Api
     */
    public function dontCheckToken()
    {
        $this->tokenCheck = false;

        return $this;
    }

    /**
     * Sends a GET request to Telegram Bot API and returns the result.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return TelegramResponse
     * @throws TelegramException
     */
    protected function get($endpoint, $params = [])
    {
        return $this->sendRequest('GET', $endpoint, $params);
    }

    /**
     * Sends a POST request to Telegram Bot API and returns the result.
     *
     * @param string $endpoint
     * @param array $params
     * @param bool $fileUpload Set true if a file is being uploaded.
     *
     * @return TelegramResponse
     * @throws TelegramException
     */
    protected function post($endpoint, array $params = [], $fileUpload = false)
    {
        if ($fileUpload) {
            $params = ['multipart' => $params];
        }
        else {
            $params = ['form_params' => $params];
        }

        return $this->sendRequest('POST', $endpoint, $params);
    }

    /**
     * Sends a multipart/form-data request to Telegram Bot API and returns the result.
     * Used primarily for file uploads.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return Message
     * @throws TelegramException
     */
    protected function uploadFile($endpoint, array $params = [])
    {
        $i                = 0;
        $multipart_params = [];
        foreach ($params as $name => $contents) {
            if (is_null($contents)) {
                continue;
            }

            if (!is_resource($contents) && $name !== 'url') {
                $validUrl = filter_var($contents, FILTER_VALIDATE_URL);
                $contents = (is_file($contents) || $validUrl) ? (new InputFile($contents))->open() : (string) $contents;
            }

            $multipart_params[$i]['name']     = $name;
            $multipart_params[$i]['contents'] = $contents;
            ++$i;
        }

        $response = $this->post($endpoint, $multipart_params, true);

        return $this->makeMessageCollection($response);
    }

    /**
     * Main Api Request Method
     *
     * Sends a request to Telegram Bot API and returns the result.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     *
     * @return TelegramResponse
     * @throws TelegramException
     */
    protected function sendRequest($method, $endpoint, array $params = [])
    {
        $request = $this->makeRequestInstance($method, $endpoint, $params);

        return $this->lastResponse = $this->client->sendRequest($request);
    }

    /**
     * Instantiates a new Request entity.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     *
     * @return TelegramRequest
     * @throws TelegramTokenNotProvidedException
     */
    public function makeRequestInstance($method, $endpoint, array $params = [])
    {
        return $this->lastRequest = new TelegramRequest($this->getAccessToken(), $method, $endpoint, $params, $this->isAsyncRequest);
    }

    /**
     * Magic method to process any "get" requests.
     *
     * @param $method
     * @param $arguments
     *
     * @return bool|TelegramResponse
     * @throws TelegramException
     */
    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 3);
        if ($action === 'get') {
            /* @noinspection PhpUndefinedFunctionInspection */
            $class_name = Str::studly(substr($method, 3));
            $class      = 'SaliBhdr\TyphoonTelegram\Telegram\Response\Models\\' . $class_name;
            $response   = $this->post($method, $arguments[0] ? : []);

            if (class_exists($class)) {
                return new $class($response->getDecodedBody());
            }

            return $response;
        }

        return false;
    }

    /**
     * Set the IoC Container.
     *
     * @param $container Container instance
     *
     * @return void
     */
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    /**
     * Get the IoC Container.
     * @return Container
     */
    public function getContainer()
    {
        return self::$container;
    }

    /**
     * Check if IoC Container has been set.
     * @return boolean
     */
    public function hasContainer()
    {
        return self::$container !== null;
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|Message
     */
    protected function makeChatMembersCountCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, MembersCount::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|Message
     */
    protected function makeChatAdministratorsCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, ChatAdministrators::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|Message
     */
    protected function makeChatMemberCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, ChatMember::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|Message
     */
    protected function makeChatCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, Chat::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|Message
     */
    protected function makeMessageCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, Message::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|Message
     */
    protected function makeBoolResponseCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, BoolModel::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|User
     */
    protected function makeUserCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, User::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|User
     */
    protected function makeUserProfilePhotosCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, UserProfilePhotos::class)->respond();
    }

    /**
     * @param TelegramResponse $response
     *
     * @return BaseModel|User
     */
    protected function makeFileCollection(TelegramResponse $response)
    {
        return ModelDecorator::make($response, File::class)->respond();
    }


}
