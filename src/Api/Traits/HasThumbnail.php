<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Traits;

trait HasThumbnail
{

    protected $thumbnail;

    public function thumb($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumb()
    {
        return $this->thumbnail;
    }
}