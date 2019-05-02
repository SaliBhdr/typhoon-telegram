<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Facades\TyTelegram;

class GetMe
{
    public function method() : string
    {
        return 'getMe';
    }

    public function send()
    {
        return TyTelegram::getMe();
    }
}