<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendMethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesWebPagePreview;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasText;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendMessage extends SendMethodAbstract
{
    use DisablesWebPagePreview,
        HasText,
        HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage;

    public function method() : string
    {
        return 'sendMessage';
    }

    protected function getRequiredParams() : array
    {
        return [
            'chat_id' => $this->chatId,
            'text'    => $this->text,
        ];
    }

    protected function addOptionalParams() : void
    {
        $this->addParam('disable_web_page_preview', $this->disableWebPagePreview);
        $this->addParam('parse_mode', $this->parsMode);
        $this->addParam('disable_notification', $this->disableNotification);
        $this->addParam('reply_to_message_id', $this->replyToMessageId);
        $this->addParam('reply_markup', $this->replyMarkup);
    }

    protected function requiredParams() : array
    {
        return [];
        return ['chat_id', 'text'];
    }
}