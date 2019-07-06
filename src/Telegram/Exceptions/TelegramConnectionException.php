<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

use Throwable;

class TelegramConnectionException extends TelegramException
{
    public function __construct(string $message = "", int $code = 500, Throwable $previous = null)
    {
        $message = 'Unable to connect! ' . $message;

        parent::__construct($message, $code, $previous);
    }
}