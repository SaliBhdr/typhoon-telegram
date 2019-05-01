<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 10:10 PM
 */

namespace Salibhdr\TyphoonTelegram\Exceptions;

use Salibhdr\TyphoonTelegram\Api\Finals\ApiRequest;
use Telegram\Bot\Exceptions\TelegramSDKException;

class RequestMethodInvalidException extends TelegramSDKException
{
    public function __construct()
    {
        $methods = implode([ApiRequest::GET, ApiRequest::POST, ApiRequest::MULTIPART]);

        $message = "Request Methods can be one of these : {$methods}";

        parent::__construct($message, 400);
    }
}