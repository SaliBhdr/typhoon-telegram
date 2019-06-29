<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:26 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasDocument
{
    protected $document;

    public function document($document)
    {
        $this->document = $document;

        return $this;
    }
}