<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;

trait DisablesNotification
{
    protected $disableNotification;

    public function disableNotification()
    {
        $this->disableNotification = true;

        return $this;
    }
}