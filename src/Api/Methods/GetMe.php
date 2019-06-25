<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Methods;

use SaliBhdr\TyphoonTelegram\Facades\Telegram;

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