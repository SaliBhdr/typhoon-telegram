<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Traits;

trait HasReplyMarkUp
{

    protected $parsMode;

    public function replyMarkup(array $replyMarkup)
    {
        $this->replyMarkup = $replyMarkup;

        return $this;
    }

    public function getReplyMarkup(): ?string
    {
        return $this->replyMarkup;
    }
}