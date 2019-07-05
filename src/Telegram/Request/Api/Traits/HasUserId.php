<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:08 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasUserId
{
    protected $userId;

    public function userId(int $userId)
    {
        $this->userId = $userId;

        return $this;
    }
}