<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/29/2019
 * Time: 11:26 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasPhoto
{
    protected $photo;

    public function photo($photo)
    {
        $this->photo = $photo;

        return $this;
    }
}