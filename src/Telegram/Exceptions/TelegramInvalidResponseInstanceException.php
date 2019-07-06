<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

class TelegramInvalidResponseInstanceException extends TelegramException
{
    public function __construct()
    {
        $message = "Second constructor argument 'response' must be instance of ResponseInterface or PromiseInterface";

        parent::__construct($message, 500);
    }
}