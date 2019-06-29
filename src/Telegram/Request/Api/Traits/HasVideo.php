<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:37 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasVideo
{
    protected $video;


    public function video($video)
    {
        $this->video = $video;

        return $this;
    }
}