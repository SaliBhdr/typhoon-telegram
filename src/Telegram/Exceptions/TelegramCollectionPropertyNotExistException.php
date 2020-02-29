<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

class TelegramCollectionPropertyNotExistException extends TelegramException
{
    public function __construct(string $key)
    {
        $message = "Property [{$key}] does not exist on this collection instance.";

        parent::__construct($message, 500);
    }
}