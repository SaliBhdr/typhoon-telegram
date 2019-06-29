<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:38 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasVoice
{
    protected $voice;

    public function voice($voice)
    {
        $this->voice = $voice;

        return $this;
    }

}