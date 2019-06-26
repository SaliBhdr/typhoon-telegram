<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;

trait HasReplyMarkUp
{

    protected $parsMode;
    
    protected $replyMarkup;

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