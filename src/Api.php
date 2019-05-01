<?php

namespace Salibhdr\TyphoonTelegram;

use Salibhdr\TyphoonTelegram\Api\Interfaces\BaseSendMessageInterface;
use Salibhdr\TyphoonTelegram\Api\Methods\GetMe;
use Salibhdr\TyphoonTelegram\Api\Methods\SendDynamic;
use Salibhdr\TyphoonTelegram\HttpClients\GuzzleHttpClient;
use Salibhdr\TyphoonTelegram\Objects\Dynamic;
use Telegram\Bot\Api as BaseApi;

/*** Class Api.
 */
class Api extends BaseApi
{
    const VERSION = '1.0.0';

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
     * @param BaseSendMessageInterface $apiObj
     * @return mixed
     * @throws Exceptions\TelegramParamsRequiredException
     */
    public function send(BaseSendMessageInterface $apiObj)
    {
        if($apiObj instanceof SendDynamic){
            return new Dynamic($this->{$apiObj->getRequestMethod()}($apiObj->sendMethod(), $apiObj->getParams()));
        }

        if($apiObj instanceof GetMe){
            return $this->{$apiObj->sendMethod()};
        }

        return $this->{$apiObj->sendMethod()}($apiObj->getParams());
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


}
