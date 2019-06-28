<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 12:57 PM
 */

namespace Salibhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasChatId
{
    protected $chatId;

    public function chatId($chatId)
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getChatId()
    {
        return $this->chatId;
    }
}