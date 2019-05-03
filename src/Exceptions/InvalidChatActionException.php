<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace Salibhdr\TyphoonTelegram\Exceptions;

use Telegram\Bot\Exceptions\TelegramSDKException;

class InvalidChatActionException extends TelegramSDKException
{
    public function __construct(array $chatActions)
    {
        $message = 'Invalid Action! Accepted values: '.implode(', ', $validActions);

        parent::__construct($message, 400);
    }
}