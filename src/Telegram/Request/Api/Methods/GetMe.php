<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\MethodAbstract;

class GetMe extends MethodAbstract
{
    public function method() : string
    {
        return 'getMe';
    }

    protected function getRequiredParams() : array
    {
        return [];
    }

    protected function addOptionalParams() : void
    {
        return;
    }

    protected function requiredParams() : array
    {
        return [];
    }
}