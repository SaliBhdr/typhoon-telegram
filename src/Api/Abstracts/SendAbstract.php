<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:50 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Abstracts;

use Salibhdr\TyphoonTelegram\Api\Interfaces\BaseSendMessageInterface;

abstract class SendAbstract extends BaseAbstract implements BaseSendMessageInterface
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