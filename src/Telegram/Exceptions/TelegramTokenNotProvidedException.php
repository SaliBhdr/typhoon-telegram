<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;
use SaliBhdr\TyphoonTelegram\Telegram\Api;

/**
 * Class TelegramSDKException.
 */
class TelegramTokenNotProvidedException extends TelegramException
{
    public function __construct(string $message = 'Required "token" not supplied in config and could not find fallback environment variable "' . Api::BOT_TOKEN_ENV_NAME . '"', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
