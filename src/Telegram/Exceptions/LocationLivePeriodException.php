<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

class LocationLivePeriodException extends TelegramException
{
    public function __construct($min, $max)
    {
        $message = "The live period param range must be between {$min} and {$max}";

        parent::__construct($message, 400);
    }
}