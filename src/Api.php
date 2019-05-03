<?php

namespace Salibhdr\TyphoonTelegram;

use Salibhdr\TyphoonTelegram\Api\Interfaces\BaseInterface;
use Salibhdr\TyphoonTelegram\Api\Methods\GetMe;
use Salibhdr\TyphoonTelegram\Api\Methods\SendDynamic;
use Salibhdr\TyphoonTelegram\Exceptions\InvalidChatActionException;
use Salibhdr\TyphoonTelegram\HttpClients\GuzzleHttpClient;
use Salibhdr\TyphoonTelegram\Objects\Dynamic;
use Telegram\Bot\Api as BaseApi;

/*** Class Api.
 */
class Api extends BaseApi
{
    const VERSION = '1.0.0';

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

    /**    * Instantiates a new Telegram super-class object.
     *
     *
     * @param string $token The Telegram Bot API Access Token.
     * @param bool $async (Optional) Indicates if the request to Telegram
     *                                                        will be asynchronous (non-blocking).
     * @param string|\Telegram\Bot\HttpClients\HttpClientInterface $http_client_handler (Optional) Custom HTTP Client Handler.
     *
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function __construct(?string $token = null, bool $async = false, $http_client_handler = null)
    {
        if (!isset($http_client_handler)) {
            $http_client_handler = new GuzzleHttpClient();
        }

        parent::__construct($token, $async, $http_client_handler);
    }

    /**    * Handle dynamic static method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'bot')
            return (new static)->$method(...$parameters);

        return static::$method(...$parameters);
    }

    /**
     * @param $apiMethodObj
     * @return mixed
     * @throws Exceptions\TelegramParamsRequiredException
     */

    public function send(BaseInterface $apiMethodObj)
    {

        if ($apiMethodObj instanceof SendDynamic) {
            return new Dynamic($this->{$apiMethodObj->getRequestMethod()}($apiMethodObj->method(), $apiMethodObj->getParams()));
        }

        if ($apiMethodObj instanceof GetMe) {
            return $this->{$apiMethodObj->method()};

        }

        return $this->{$apiMethodObj->sendMethod()}($apiMethodObj->getParams());

    }

    /**
     * The default headers used with every request.
     *
     * @return array
     */
    public function getDefaultHeaders()
    {
        return [
            'User-Agent' => 'Telegram Bot PHP SDK v' . Api::VERSION . ' - (https://github.com/salibhdr/typhoon-telegram-bot)',
        ];
    }

    /**
     * @param array $keyboard
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
     * @return \Telegram\Bot\TelegramResponse
     * @throws InvalidChatActionException
     */
    public function sendChatAction(array $params)
    {
        if (isset($params['action']) && in_array($params['action'], $this->chatActions)) {
            return $this->post('sendChatAction', $params);
        }

        throw new InvalidChatActionException($this->chatActions);
    }

    public function bot(string $botName)
    {
        $bot = config('telegram.bots.' . $botName);

        if (!is_null($bot)
            && is_array($bot)
            && empty($bot)
            && isset($bot['botToken'])
            && isset($bot['is_active'])
            && $bot['is_active'])
            $this->setAccessToken($bot['botToken']);

        return $this;
    }

    /**
     * Magic method to process any "get" requests.
     *
     * @param $method
     * @param $arguments
     *
     * @return bool|TelegramResponse
     */
    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 3);
        if ($action === 'get') {
            /* @noinspection PhpUndefinedFunctionInspection */
            $class_name = studly_case(substr($method, 3));
            $class = 'Telegram\Bot\Objects\\'.$class_name;
            $response = $this->post($method, $arguments[0] ?: []);

            if (!class_exists($class)) {
                $class = 'Salibhdr\TyphoonTelegram\Objects\\'.$class_name;
            }

            if(class_exists($class)){
                return new $class($response->getDecodedBody());
            }

            return $response;
        }

        return false;
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
     * @param array    $params
     *
     * @var int|string $params ['chat_id']
     * @var string     $params ['document']
     * @var int        $params ['reply_to_message_id']
     * @var string     $params ['reply_markup']
     *
     * @return Message
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
}
