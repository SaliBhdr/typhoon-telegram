<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 10:10 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Exceptions;

class TelegramParamsRequiredException extends TelegramSDKException
{
    public function __construct(array $requiredParams)
    {
        parent::__construct($this->getMsgText($requiredParams), 422);
    }

    private function getMsgText(array $requiredParams)
    {
        if (empty($requiredParams))
            return 'No params has been set';

        $params = implode(', ',$requiredParams);

        if (count($requiredParams) > 1)
            $message = "{$params} parameters are empty, please provide these parameters";
        else
            $message = "{$params} parameter is empty, please provide this parameter";

        return $message;
    }
}