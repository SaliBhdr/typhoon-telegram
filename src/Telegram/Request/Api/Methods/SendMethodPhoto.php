<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendMethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Captionable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasPhoto;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendMethodPhoto extends SendMethodAbstract
{
    use HasPhoto,
        HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable;

    public function method() : string
    {
        return 'sendPhoto';
    }

    protected function getRequiredParams() : array
    {
        return [
            'chat_id' => $this->chatId,
            'photo'   => $this->photo,
        ];
    }

    protected function addOptionalParams() : void
    {
        $this->addParam('caption', $this->caption);
        $this->addParam('parse_mode', $this->parsMode);
        $this->addParam('disable_notification', $this->disableNotification);
        $this->addParam('reply_to_message_id', $this->replyToMessageId);
        $this->addParam('reply_markup', $this->replyMarkup);
    }

    protected function requiredParams() : array
    {
        return ['chat_id', 'photo'];
    }
}