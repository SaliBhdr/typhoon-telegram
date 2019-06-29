<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\SendInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendMessage extends SendAbstract implements SendInterface
{
    use HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage;

    protected $text;

    protected $disableWebPagePreview;


    public function text($text): SendMessage
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }


    public function disableWebPagePreview(): SendMessage
    {
        $this->disableWebPagePreview = true;

        return $this;
    }

    public function isWebPagePreviewDisabled(): ?bool
    {
        return $this->disableNotification;
    }


    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'text'    => $this->getText(),
        ];
    }

    protected function addOptionalParams(): void
    {
        if (!is_null($this->getParsMode())) {
            $this->extraParam('parse_mode', $this->getParsMode());
        }

        if (!is_null($this->isWebPagePreviewDisabled())) {
            $this->extraParam('disable_web_page_preview', $this->isWebPagePreviewDisabled());
        }

        if (!is_null($this->isNotificationDisabled())) {
            $this->extraParam('disable_notification', $this->isNotificationDisabled());
        }

        if (!is_null($this->getReplyToMessageId())) {
            $this->extraParam('reply_to_message_id', $this->getReplyToMessageId());
        }

        if (!is_null($this->getReplyMarkup())) {
            $this->extraParam('reply_markup', $this->getReplyMarkup());
        }

    }

    public function method(): string
    {
        return 'sendMessage';
    }

    protected function requiredParams(): array
    {
//        return [];
        return ['chat_id', 'text'];
    }
}