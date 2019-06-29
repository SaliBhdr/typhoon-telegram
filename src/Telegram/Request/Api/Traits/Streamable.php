<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:33 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait Streamable
{
    protected $supportStreaming;

    public function supportsStreaming()
    {
        $this->supportStreaming = true;

        return $this;
    }
}