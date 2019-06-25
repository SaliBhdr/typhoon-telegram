<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 10:10 PM
 */

namespace SaliBhdr\TyphoonTelegram\Exceptions;

use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramParamsRequiredException extends TelegramSDKException
{
    public function __construct(array $requiredParams)
    {
        if(!empty($requiredParams)){
            $requiredParams = implode($requiredParams);

            $message = "{$requiredParams} params are required";
        }else{
            $message = 'No params has been set';
        }


        parent::__construct($message, 422);
    }
}