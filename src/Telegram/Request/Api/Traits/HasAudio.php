<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:20 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasAudio
{
    protected $audio;

    public function audio($audio)
    {
        $this->audio = $audio;

        return $this;
    }

}