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
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\TelegramCollection;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\File;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Message;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\ModelDecorator;
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
    protected $accessToken = null;

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
    protected $commandBus = null;

    /**
     * @var Container IoC Container
     */
    protected static $container = null;

    /**
     * @var Api $instance
     */
    protected static $instance;

    /**
     * Timeout of the request in seconds.
     *
     * @var int
     */
    protected $timeOut = 60;


    protected $tokenCheck = true;

    /**
     * Connection timeout of the request in seconds.
     *
     * @var int
     */
    protected $connectTimeOut = 10;

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
        'upload_video_note '
    ];

    public function setChatActions(array $chatActions)
    {
        $this->chatActions = $chatActions;
    }

    /**
     * Instantiates a new Telegram super-class object.
     *
     *
     * @param string $token The Telegram Bot API Access Token.
     * @param bool $async (Optional) Indicates if the request to Telegram
     *                                                        will be asynchronous (non-blocking).
     * @param string|HttpClientInterface $http_client_handler (Optional) Custom HTTP Client Handler.
     *
     */
    private function __construct(string $token = null, bool $async = false, $http_client_handler = null)
    {

        $this->setDefaultAccessToken($token);

        if (isset($async)) {
            $this->setAsyncRequest($async);
        }

        $this->setHttpClientHandler($http_client_handler);
    }

    /**
     * @param $token
     */
    private function setDefaultAccessToken($token)
    {
        if (isset($token))
            $this->accessToken = $token;
        else
            $this->bot('default');
    }

    protected function setHttpClientHandler($http_client_handler)
    {
        if (!isset($http_client_handler) || $http_client_handler === 'guzzle') {
            $http_client_handler = new GuzzleHttpClient();
        } else {
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
     * @throws TelegramException
     */
    public static function init(string $token = null, bool $async = false, $http_client_handler = null)
    {
        if (empty(static::$instance))
            static::$instance = new static($token, $async, $http_client_handler);

        return static::$instance;
    }

    /**    * Handle dynamic static method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     * @throws TelegramException
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
     *
     * <code>
     * $params = [
     *   'chat_id' => '',
     *   'action'  => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendchataction
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['action']
     *
     * @throws TelegramException
     *
     * @return TelegramResponse
     */

    /**
     * Broadcast a Chat Action.
     *
     * <code>
     * $params = [
     *   'chat_id' => '',
     *   'action'  => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendchataction
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['action']
     *
     * @throws TelegramException
     *
     * @return TelegramResponse
     *
     * @throws TelegramInvalidChatActionException
     */
    public function sendChatAction(array $params)
    {
        if (isset($params['action']) && in_array($params['action'], $this->chatActions)) {
            return $this->post('sendChatAction', $params);
        }

        throw new TelegramInvalidChatActionException($this->chatActions);
    }

    /**
     * @param string $botName
     *
     * @return $this
     */
    public function bot(string $botName)
    {

        $bot = config('telegram.bots.' . $botName);

        if (!is_null($bot)
            && is_array($bot)
            && !empty($bot)
            && isset($bot['botToken'])
            && isset($bot['is_active'])
            && $bot['is_active']) {

            $this->setAccessToken($bot['botToken']);

        }


        return $this;
    }


    /**
     * Send general files.
     *
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'document'            => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#senddocument
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['document']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @return Message
     *
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

    public function getChat(array $params)
    {
        return $this->post('getChat', $params);
    }

    public function getChatAdministrators(array $params)
    {
        return $this->post('getChatAdministrators', $params);
    }

    public function getChatMembersCount(array $params)
    {
        return $this->post('getChatMembersCount', $params);
    }

    public function getChatMember(array $params)
    {
        return $this->post('getChatMember', $params);
    }

    public function setChatStickerSet(array $params)
    {
        return $this->post('setChatStickerSet', $params);
    }

    public function answerCallbackQuery(array $params)
    {
        return $this->post('answerCallbackQuery', $params);
    }

    /**
     **********************************************************************************/


    /**
     * Returns the Client service.
     *
     * @return TelegramClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns Telegram Bot API Access Token.
     *
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
     *
     * @return TelegramResponse
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Sets the bot access token to use with API requests.
     *
     * @param string $accessToken The bot access token to save.
     *
     * @throws \InvalidArgumentException
     *
     * @return Api
     */
    public function setAccessToken($accessToken)
    {
        if (is_string($accessToken)) {
            $this->accessToken = $accessToken;

            return $this;
        }

        throw new \InvalidArgumentException('The Telegram bot access token must be of type "string"');
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
     * Check if this is an asynchronous request (non-blocking).
     *
     * @return bool
     */
    public function isAsyncRequest()
    {
        return $this->isAsyncRequest;
    }

    /**
     * Returns SDK's Command Bus.
     *
     * @return CommandBus
     * @throws TelegramException
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
     * @throws TelegramException
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
     * @throws TelegramException
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
     * @throws TelegramException
     */
    public function removeCommands(array $names)
    {
        return $this->getCommandBus()->removeCommands($names);
    }

    /**
     * Returns list of available commands.
     *
     * @return Commands\Command[]
     * @throws TelegramException
     */
    public function getCommands()
    {
        return $this->getCommandBus()->getCommands();
    }

    /**
     * A simple method for testing your bot's auth token.
     * Returns basic information about the bot in form of a User object.
     *
     * @link https://core.telegram.org/bots/api#getme
     *
     * @return User|BaseModel
     * @throws TelegramException
     */
    public function getMe()
    {
        $response = $this->post('getMe');

        return $this->makeUserCollection($response);
    }

    /**
     * Send text messages.
     *
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
     *
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
     *
     * @return Message|BaseModel
     * @throws TelegramException
     */
    public function sendMessage(array $params)
    {
        $response = $this->post('sendMessage', $params);

        return $this->makeMessageCollection($response);
    }

    /**
     * Forward messages of any kind.
     *
     * <code>
     * $params = [
     *   'chat_id'      => '',
     *   'from_chat_id' => '',
     *   'message_id'   => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#forwardmessage
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var int $params ['from_chat_id']
     * @var int $params ['message_id']
     *
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
     *
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'photo'               => '',
     *   'caption'             => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendphoto
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['photo']
     * @var string $params ['caption']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @return Message
     * @throws TelegramException
     */
    public function sendPhoto(array $params)
    {
        return $this->uploadFile('sendPhoto', $params);
    }

    /**
     * Send regular audio files.
     *
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
     *
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
     *
     * @return Message
     * @throws TelegramException
     */
    public function sendAudio(array $params)
    {
        return $this->uploadFile('sendAudio', $params);
    }

    /**
     * Send general files.
     *
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'document'            => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#senddocument
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['document']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @return Message
     * @throws TelegramException
     */
    public function sendDocument(array $params)
    {
        return $this->uploadFile('sendDocument', $params);
    }

    /**
     * Send .webp stickers.
     *
     * <code>
     * $params = [
     *   'chat_id' => '',
     *   'sticker' => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup' => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendsticker
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['sticker']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @throws TelegramException
     *
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
     *
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
     *
     * @see  sendDocument
     * @link https://core.telegram.org/bots/api#sendvideo
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['video']
     * @var int $params ['duration']
     * @var string $params ['caption']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @return Message
     * @throws TelegramException
     */
    public function sendVideo(array $params)
    {
        return $this->uploadFile('sendVideo', $params);
    }

    /**
     * Send voice audio files.
     *
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'voice'               => '',
     *   'duration'            => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendaudio
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['voice']
     * @var int $params ['duration']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @return Message
     * @throws TelegramException
     */
    public function sendVoice(array $params)
    {
        return $this->uploadFile('sendVoice', $params);
    }

    /**
     * Send point on the map.
     *
     * <code>
     * $params = [
     *   'chat_id'             => '',
     *   'latitude'            => '',
     *   'longitude'           => '',
     *   'reply_to_message_id' => '',
     *   'reply_markup'        => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendlocation
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var float $params ['latitude']
     * @var float $params ['longitude']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @return Message|BaseModel
     *
     * @throws TelegramException
     */
    public function sendLocation(array $params)
    {
        $response = $this->post('sendLocation', $params);

        return $this->makeMessageCollection($response);
    }


    /**
     * Returns a list of profile pictures for a user.
     *
     * <code>
     * $params = [
     *   'user_id' => '',
     *   'offset'  => '',
     *   'limit'   => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#getuserprofilephotos
     *
     * @param array $params
     *
     * @var int $params ['user_id']
     * @var int $params ['offset']
     * @var int $params ['limit']
     *
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
     *
     * <code>
     * $params = [
     *   'file_id' => '',
     * ];
     * </code>
     *
     * The file can then be downloaded via the link
     * https://api.telegram.org/file/bot<token>/<file_path>,
     * where <file_path> is taken from the response.
     *
     * @link https://core.telegram.org/bots/api#getFile
     *
     * @param array $params
     *
     * @var string $params ['file_id']
     *
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
     *
     * <code>
     * $params = [
     *   'url'         => '',
     *   'certificate' => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#setwebhook
     *
     * @param array $params
     *
     * @var string $params ['url']         HTTPS url to send updates to.
     * @var string $params ['certificate'] Upload your public key certificate so that the root certificate in
     *                                      use can be checked.
     * @return Message
     * @throws TelegramException
     *
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
     *
     * @see setWebhook
     *
     * @return Update
     */
    public function getWebhookUpdates()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        return new Update($body);
    }

    /**
     * Removes the outgoing webhook (if any).
     *
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
     *
     * <code>
     * $params = [
     *   'offset'  => '',
     *   'limit'   => '',
     *   'timeout' => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#getupdates
     *
     * @param array $params
     *
     * @var int|null $params ['offset']
     * @var int|null $params ['limit']
     * @var int|null $params ['timeout']
     *
     * @return Update[]
     * @throws TelegramException
     */
    public function getUpdates(array $params = [])
    {
        $response = $this->post('getUpdates', $params);
        $updates = $response->getDecodedBody();

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
     *
     * <code>
     * $params = [
     *   'keyboard'          => '',
     *   'resize_keyboard'   => '',
     *   'one_time_keyboard' => '',
     *   'selective'         => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#replykeyboardmarkup
     *
     * @param array $params
     *
     * @var array $params ['keyboard']
     * @var bool $params ['resize_keyboard']
     * @var bool $params ['one_time_keyboard']
     * @var bool $params ['selective']
     *
     * @return string
     */
    public function replyKeyboardMarkup(array $params)
    {
        return json_encode($params);
    }

    /**
     * Hide the current custom keyboard and display the default letter-keyboard.
     *
     * <code>
     * $params = [
     *   'hide_keyboard' => true,
     *   'selective'     => false,
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#replykeyboardhide
     *
     * @param array $params
     *
     * @var bool $params ['hide_keyboard']
     * @var bool $params ['selective']
     *
     * @return string
     */
    public static function replyKeyboardHide(array $params = [])
    {
        return json_encode(array_merge(['hide_keyboard' => true, 'selective' => false], $params));
    }

    /**
     * Display a reply interface to the user (act as if the user has selected the bot‘s message and tapped ’Reply').
     *
     * <code>
     * $params = [
     *   'force_reply' => true,
     *   'selective'   => false,
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#forcereply
     *
     * @param array $params
     *
     * @var bool $params ['force_reply']
     * @var bool $params ['selective']
     *
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
    public function commandsHandler($webhook = false)
    {
        if ($webhook) {
            $update = $this->getWebhookUpdates();
            $this->processCommand($update);

            return $update;
        }

        $updates = $this->getUpdates();
        $highestId = -1;

        foreach ($updates as $update) {
            $highestId = $update->getUpdateId();
            $this->processCommand($update);
        }

        //An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id.
        if ($highestId != -1) {
            $params = [];
            $params['offset'] = $highestId + 1;
            $params['limit'] = 1;
            $this->getUpdates($params);
        }

        return $updates;
    }

    /**
     * Check update object for a command and process.
     *
     * @param Update $update
     *
     * @throws TelegramException
     */
    protected function processCommand(Update $update)
    {
        $message = $update->getMessage();

        if ($message !== null && $message->has('text')) {
            $this->getCommandBus()->handler($message->getText(), $update);
        }
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

        return $object->keys()
                      ->intersect($types)
                      ->pop();
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
    public function tokenMustNotCheck()
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
     * @throws TelegramException
     *
     * @return TelegramResponse
     */
    protected function get($endpoint, $params = [])
    {
        return $this->sendRequest(
            'GET',
            $endpoint,
            $params
        );
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
        } else {
            $params = ['form_params' => $params];
        }

        return $this->sendRequest(
            'POST',
            $endpoint,
            $params
        );
    }

    /**
     * Sends a multipart/form-data request to Telegram Bot API and returns the result.
     * Used primarily for file uploads.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @throws TelegramException
     *
     * @return Message
     */
    protected function uploadFile($endpoint, array $params = [])
    {
        $i = 0;
        $multipart_params = [];
        foreach ($params as $name => $contents) {
            if (is_null($contents)) {
                continue;
            }

            if (!is_resource($contents) && $name !== 'url') {
                $validUrl = filter_var($contents, FILTER_VALIDATE_URL);
                $contents = (is_file($contents) || $validUrl) ? (new InputFile($contents))->open() : (string) $contents;
            }

            $multipart_params[$i]['name'] = $name;
            $multipart_params[$i]['contents'] = $contents;
            ++$i;
        }

        $response = $this->post($endpoint, $multipart_params, true);

        return $this->makeMessageCollection($response);
    }

    /**
     * Sends a request to Telegram Bot API and returns the result.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     *
     * @return TelegramResponse
     * @throws TelegramException
     */
    protected function sendRequest(
        $method,
        $endpoint,
        array $params = []
    )
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
    public function makeRequestInstance(
        $method,
        $endpoint,
        array $params = []
    )
    {
        return $this->lastRequest = new TelegramRequest(
            $this->getAccessToken(),
            $method,
            $endpoint,
            $params,
            $this->isAsyncRequest(),
            $this->getTimeOut(),
            $this->getConnectTimeOut()
        );
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
            $class = 'SaliBhdr\TyphoonTelegram\Telegram\Response\Models\\' . $class_name;
            $response = $this->post($method, $arguments[0] ? : []);

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
     *
     * @return Container
     */
    public function getContainer()
    {
        return self::$container;
    }

    /**
     * Check if IoC Container has been set.
     *
     * @return boolean
     */
    public function hasContainer()
    {
        return self::$container !== null;
    }

    /**
     * @return int
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * @param int $timeOut
     *
     * @return $this
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectTimeOut()
    {
        return $this->connectTimeOut;
    }

    /**
     * @param int $connectTimeOut
     *
     * @return $this
     */
    public function setConnectTimeOut($connectTimeOut)
    {
        $this->connectTimeOut = $connectTimeOut;

        return $this;
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
