<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Interfaces\SendAnimationInterface;
use Salibhdr\TyphoonTelegram\Api\Traits\Captionable;
use Salibhdr\TyphoonTelegram\Api\Traits\DisablesNotification;
use Salibhdr\TyphoonTelegram\Api\Traits\HasDimensions;
use Salibhdr\TyphoonTelegram\Api\Traits\HasDuration;
use Salibhdr\TyphoonTelegram\Api\Traits\HasThumbnail;
use Salibhdr\TyphoonTelegram\Api\Traits\Parsable;
use Salibhdr\TyphoonTelegram\Api\Traits\HasReplyMarkUp;
use Salibhdr\TyphoonTelegram\Api\Traits\RepliesToMessage;
use Salibhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;

class SendAnimation extends SendAbstract implements SendAnimationInterface
{
    use HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable,
        HasDuration,
        HasThumbnail,
        HasDimensions;

    protected $animation;

    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'animation' => $this->getAnimation(),
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

        if (!is_null($this->getThumb())) {
            $this->params['thumb'] = $this->getThumb();
        }
    }

    public function method(): string
    {
       return 'sendAnimation';
    }

    protected function requiredParams(): array
    {
        return ['chat_id','animation'];
    }

    public function animation($animation)
    {
        $this->animation = $animation;

        return $this;
    }

    public function getAnimation()
    {
        return $this->animation;
    }
}