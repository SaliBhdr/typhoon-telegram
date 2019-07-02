<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

use Throwable;

/**
 * Class TelegramSDKException.
 */
class TelegramException extends \Exception
{
    public function __construct(string $message = "", int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
