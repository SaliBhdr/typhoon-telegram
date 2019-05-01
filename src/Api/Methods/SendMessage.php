<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Interfaces\SendMessageInterface;
use Salibhdr\TyphoonTelegram\Api\Traits\DisablesNotification;
use Salibhdr\TyphoonTelegram\Api\Traits\Parsable;
use Salibhdr\TyphoonTelegram\Api\Traits\HasReplyMarkUp;
use Salibhdr\TyphoonTelegram\Api\Traits\RepliesToMessage;
use Salibhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;

class SendMessage extends SendAbstract implements SendMessageInterface
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
            'text' => $this->getText(),
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

    public function sendMethod(): string
    {
        return 'sendMessage';
    }

    protected function requiredParams(): array
    {
        return ['chat_id', 'text'];
    }
}