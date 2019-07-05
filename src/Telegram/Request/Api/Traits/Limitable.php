<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:09 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait Limitable
{
    protected $limit;

    public function limit(int $limit)
    {
            $this->limit = $limit;

        return $this;
    }
}