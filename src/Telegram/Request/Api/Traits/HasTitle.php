<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:20 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasTitle
{
    protected $title;

    public function title(string $trackName)
    {
        $this->title = $trackName;

        return $this;
    }

}