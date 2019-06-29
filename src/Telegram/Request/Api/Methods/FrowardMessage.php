<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;


use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasMessageForward;

class FrowardMessage extends SendAbstract
{
    use HasMessageForward,
        DisablesNotification;

    public function method() : string
    {
        return 'forwardMessage';
    }

    protected function getRequiredParams() : array
    {
        return [
            'chat_id'      => $this->chatId,
            'from_chat_id' => $this->fromChatId,
            'message_id'   => $this->messageId,
        ];
    }

    protected function addOptionalParams() : void
    {
        $this->addParam('disable_notification', $this->disableNotification);
    }

    protected function requiredParams() : array
    {
        return ['chat_id', 'from_chat_id', 'message_id'];
    }

}