<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

class TelegramInvalidChatActionException extends TelegramException
{
    public function __construct(array $chatActions)
    {
        $message = 'Invalid Action! Accepted values: '.implode(', ', $chatActions);

        parent::__construct($message, 400);
    }
}