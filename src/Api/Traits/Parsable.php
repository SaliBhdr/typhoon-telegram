<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Traits;

use SaliBhdr\TyphoonTelegram\Api\Finals\Parse;

trait Parsable
{

    protected $parsMode;

    public function htmlParsMode()
    {
            $this->parsMode = Parse::HTML;
    }

    public function markdownParseMode()
    {
        $this->parsMode = Parse::MARKDOWN;
    }

    public function getParsMode(): ?string
    {
        return $this->parsMode;
    }
}