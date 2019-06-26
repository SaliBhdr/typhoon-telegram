<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\SendVideoInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Captionable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasDimensions;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasDuration;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasThumbnail;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendVideo extends SendAbstract implements SendVideoInterface
{
    use HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable,
        HasDuration,
        HasThumbnail,
        HasDimensions;

    protected $video;

    protected $supportStreaming;

    public function method(): string
    {
        return 'sendVideo';
    }

    /**
     * @param mixed $video
     * @return SendVideo
     */
    public function video($video)
    {
        $this->video = $video;

        return $this;
    }

    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'video'   => $this->getVideo()
        ];
    }

    protected function addOptionalParams(): void
    {
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

        if (!is_null($this->getCaption())) {
            $this->params['caption'] = $this->getCaption();
        }

        if (!is_null($this->getDuration())) {
            $this->params['duration'] = $this->getDuration();
        }

        if (!is_null($this->getHeight())) {
            $this->params['height'] = $this->getHeight();
        }

        if (!is_null($this->getWidth())) {
            $this->params['width'] = $this->getWidth();
        }

        if (!is_null($this->isSupportsStreaming())) {
            $this->params['supports_streaming'] = $this->isSupportsStreaming();
        }
    }

    protected function requiredParams(): array
    {
        return ['chat_id', 'video'];
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function supportsStreaming()
    {
        $this->supportStreaming = true;

        return $this;
    }

    public function isSupportsStreaming(): bool
    {
        return $this->supportStreaming;
    }

}