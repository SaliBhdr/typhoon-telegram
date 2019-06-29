<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:10 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasOffset
{
    protected $offset;

    public function offset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }
}