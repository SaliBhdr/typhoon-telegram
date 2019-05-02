<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 5/2/2019
 * Time: 8:09 PM
 */

namespace Salibhdr\TyphoonTelegram\Exceptions;

use Telegram\Bot\Exceptions\TelegramSDKException;

class LivePeriodException extends TelegramSDKException
{
    public function __construct($min,$max)
    {
        $message = "The live period param must be between {$min} and {$max}";

        parent::__construct($message, 400);
    }
}