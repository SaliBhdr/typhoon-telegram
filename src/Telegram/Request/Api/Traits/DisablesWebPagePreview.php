<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/29/2019
 * Time: 11:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait DisablesWebPagePreview
{
    protected $disableWebPagePreview;


    public function disableWebPagePreview()
    {
        $this->disableWebPagePreview = TRUE;

        return $this;
    }
}