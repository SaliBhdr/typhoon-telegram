<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

/**
 * Class TelegramSDKException.
 */
class TelegramWebhookException extends TelegramException
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
