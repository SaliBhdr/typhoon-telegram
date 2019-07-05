<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/29/2019
 * Time: 11:27 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasMessageForward
{
    protected $fromChatId;

    protected $messageId;


    public function fromChatId($fromChatId)
    {
        $this->fromChatId = $fromChatId;

        return $this;
    }

    public function messageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }
}