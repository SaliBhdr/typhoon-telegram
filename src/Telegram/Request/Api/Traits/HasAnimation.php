<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:14 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasAnimation
{
    protected $animation;

    public function animation($animation)
    {
        $this->animation = $animation;

        return $this;
    }
}