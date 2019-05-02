<?php

namespace Salibhdr\TyphoonTelegram;

use Salibhdr\TyphoonTelegram\Api\Interfaces\BaseInterface;
use Salibhdr\TyphoonTelegram\Api\Methods\GetMe;
use Salibhdr\TyphoonTelegram\Api\Methods\SendDynamic;
use Salibhdr\TyphoonTelegram\Exceptions\InvalidChanActionException;
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

    public function setChatActions(array $chatActions){
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
        if(!isset($http_client_handler)){
            $http_client_handler = new GuzzleHttpClient();
        }

        parent::__construct($token, $async, $http_client_handler);
    }

    /**
     * @param $apiMethodObj
     * @return mixed
     * @throws Exceptions\TelegramParamsRequiredException
     */
    public function send($apiMethodObj)
    {
        if($apiMethodObj instanceof SendDynamic){
            return new Dynamic($this->{$apiMethodObj->getRequestMethod()}($apiMethodObj->method(), $apiMethodObj->getParams()));
        }

        if($apiMethodObj instanceof GetMe){
            return $this->{$apiMethodObj->method()};
        }

        return $this->{$apiMethodObj->method()}($apiMethodObj->getParams());
    }

    /**
     * The default headers used with every request.
     *
     * @return array
     */
    public function getDefaultHeaders()
    {
        return [
            'User-Agent' => 'Telegram Bot PHP SDK v'.Api::VERSION.' - (https://github.com/salibhdr/typhoon-telegram-bot)',
        ];
    }

    /**
     * @param array $keyboard
     * @return array
     */
    public function inlineKeyboardMarkup(array $keyboard)
    {
        if(!array_key_exists('inline_keyboard',$keyboard)){
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
     * @throws InvalidChanActionException
     */
    public function sendChatAction(array $params)
    {
        if (isset($params['action']) && in_array($params['action'], $this->chatActions)) {
            return $this->post('sendChatAction', $params);
        }

        throw new InvalidChanActionException($this->chatActions);
    }

}
