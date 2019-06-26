<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */
namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;

trait Captionable
{

    protected $caption;

    public function caption(string $caption)
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }
}