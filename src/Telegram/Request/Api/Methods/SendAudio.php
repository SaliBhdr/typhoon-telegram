<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\SendAudioInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Captionable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasDuration;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasThumbnail;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendAudio extends SendAbstract implements SendAudioInterface
{
    use HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable,
        HasDuration,
        HasThumbnail;

    protected $audio;

    protected $performer;

    protected $title;

    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'audio'   => $this->getAudio(),
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

        if (!is_null($this->getPerformer())) {
            $this->params['performer'] = $this->getPerformer();
        }

        if (!is_null($this->getTitle())) {
            $this->params['title'] = $this->getTitle();
        }

        if (!is_null($this->getThumb())) {
            $this->params['thumb'] = $this->getThumb();
        }

    }

    public function method(): string
    {
        return 'sendAudio';
    }

    protected function requiredParams(): array
    {
        return ['chat_id', 'audio'];
    }

    public function audio($audio)
    {
        $this->audio = $audio;

        return $this;
    }

    public function getAudio()
    {
        return $this->audio;
    }

    public function performer(string $performer)
    {
        $this->performer = $performer;

        return $this;
    }

    public function getPerformer(): ?string
    {
        return $this->performer;
    }

    public function title(string $trackName)
    {
        $this->title = $trackName;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

}