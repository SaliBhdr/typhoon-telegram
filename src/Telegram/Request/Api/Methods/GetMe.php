<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Laravel\Facades\Telegram;

class GetMe
{
    public function method() : string
    {
        return 'getMe';
    }

    public function send()
    {
        return Telegram::getMe();
    }
}