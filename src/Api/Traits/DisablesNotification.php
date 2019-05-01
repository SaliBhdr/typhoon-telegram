<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Traits;

trait DisablesNotification
{
    protected $disableNotification;

    public function isNotificationDisabled(): ?bool
    {
        return $this->disableNotification;
    }

    public function disableNotification()
    {
        $this->disableNotification = true;

        return $this;
    }
}