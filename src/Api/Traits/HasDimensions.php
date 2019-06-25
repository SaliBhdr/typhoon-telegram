<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */
namespace SaliBhdr\TyphoonTelegram\Api\Traits;

trait HasDimensions
{

    protected $width;

    protected $height;


    public function width(int $pixels)
    {
        $this->width = $pixels;
        return $this;
    }


    public function height(int $pixels)
    {
        $this->height = $pixels;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }
}