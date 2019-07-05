<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:30 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasLocation
{
    protected $latitude;

    protected $longitude;

    protected $livePeriod;

    public function latitude(float $latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }


    public function longitude(float $longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function livePeriod(int $livePeriod)
    {
        $this->livePeriod = $livePeriod;

        return $this;
    }
}