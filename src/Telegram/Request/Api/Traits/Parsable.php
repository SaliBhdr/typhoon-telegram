<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Finals\Parse;

trait Parsable
{

    protected $parsMode;

    public function htmlParsMode()
    {
        $this->parsMode = Parse::HTML;

        return $this;
    }

    public function markdownParseMode()
    {
        $this->parsMode = Parse::MARKDOWN;

        return $this;
    }

}