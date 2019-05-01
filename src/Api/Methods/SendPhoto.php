<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Interfaces\SendPhotoInterface;
use Salibhdr\TyphoonTelegram\Api\Traits\Captionable;
use Salibhdr\TyphoonTelegram\Api\Traits\DisablesNotification;
use Salibhdr\TyphoonTelegram\Api\Traits\Parsable;
use Salibhdr\TyphoonTelegram\Api\Traits\HasReplyMarkUp;
use Salibhdr\TyphoonTelegram\Api\Traits\RepliesToMessage;
use Salibhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;

class SendPhoto extends SendAbstract implements SendPhotoInterface
{
    use HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable;

    protected $photo;

    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'photo' => $this->getPhoto(),
        ];
    }

    protected function addOptionalParams(): void
    {
        if(!is_null($this->getCaption())){
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
    }

    public function sendMethod(): string
    {
        return 'sendPhoto';
    }

    protected function requiredParams(): array
    {
        return ['chat_id','photo'];
    }

    public function photo($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

}