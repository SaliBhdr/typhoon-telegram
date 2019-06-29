<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/29/2019
 * Time: 11:20 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasText
{
    protected $text;

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }
}