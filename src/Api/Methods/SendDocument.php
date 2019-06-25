<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Methods;

use SaliBhdr\TyphoonTelegram\Api\Interfaces\SendDocumentInterface;
use SaliBhdr\TyphoonTelegram\Api\Traits\Captionable;
use SaliBhdr\TyphoonTelegram\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Api\Traits\HasThumbnail;
use SaliBhdr\TyphoonTelegram\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Api\Traits\RepliesToMessage;
use SaliBhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;

class SendDocument extends SendAbstract implements SendDocumentInterface
{
    use HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable,
        HasThumbnail;

    protected $document;

    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'document' => $this->getDocument(),
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

        if (!is_null($this->getThumb())) {
            $this->params['thumb'] = $this->getThumb();
        }
    }

    public function method(): string
    {
       return 'sendDocument';
    }

    protected function requiredParams(): array
    {
        return ['chat_id','document'];
    }

    public function document($document)
    {
        $this->document = $document;

       return $this;
    }

    public function getDocument()
    {
        return $this->document;
    }
}