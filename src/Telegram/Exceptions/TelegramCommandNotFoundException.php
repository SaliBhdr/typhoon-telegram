<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

class TelegramCommandNotFoundException extends TelegramException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}