<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:58 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;

trait RepliesToMessage
{
    protected $replyToMessageId;

    public function replyToMessageId(int $message_id)
    {
        $this->replyToMessageId = $message_id;

        return $this;
    }

}