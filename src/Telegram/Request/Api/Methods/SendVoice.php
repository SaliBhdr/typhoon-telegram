<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\SendVoiceInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Captionable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasDuration;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendVoice extends SendAbstract implements SendVoiceInterface
{
    use HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable,
        HasDuration;

    protected $voice;

    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'voice'   => $this->getVoice()
        ];
    }

    protected function addOptionalParams(): void
    {
        if (!is_null($this->getCaption())) {
            $this->params['caption'] = $this->getCaption();
        }

        if (!is_null($this->getParsMode())) {
            $this->params['parse_mode'] = $this->getParsMode();
        }

        if (!is_null($this->isNotificationDisabled())) {
            $this->params['disable_notification'] = $this->isNotificationDisabled();
        }

        if (!is_null($this->getReplyToMessageId())) {
            $this->params['reply_to_message_id'] = $this->getReplyToMessageId();
        }

        if (!is_null($this->getReplyMarkup())) {
            $this->params['reply_markup'] = $this->getReplyMarkup();
        }

        if (!is_null($this->getDuration())) {
            $this->params['duration'] = $this->getDuration();
        }

    }

    public function method(): string
    {
        return 'sendVoice';
    }

    protected function requiredParams(): array
    {
        return ['chat_id', 'voice'];
    }

    public function voice($voice)
    {
        $this->voice = $voice;

        return $this;
    }

    public function getVoice()
    {
        return $this->voice;
    }

}