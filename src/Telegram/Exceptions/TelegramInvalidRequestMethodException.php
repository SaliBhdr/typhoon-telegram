<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 10:10 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Finals\ApiRequest;

class TelegramInvalidRequestMethodException extends TelegramException
{
    public function __construct()
    {
        $methods = implode([ApiRequest::GET, ApiRequest::POST, ApiRequest::MULTIPART]);

        $message = "Request Methods can be one of these : {$methods}";

        parent::__construct($message, 400);
    }
}