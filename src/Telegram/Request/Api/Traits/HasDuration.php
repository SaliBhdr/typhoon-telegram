<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;

trait HasDuration
{

    protected $duration;

    public function duration(int $seconds)
    {
        $this->duration = $seconds;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }
}