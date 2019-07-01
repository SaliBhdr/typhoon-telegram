<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendMethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Captionable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasAudio;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasDuration;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasPerformer;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasThumbnail;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasTitle;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendMethodAudio extends SendMethodAbstract
{
    use HasAudio,
        HasPerformer,
        HasTitle,
        HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable,
        HasDuration,
        HasThumbnail;

    public function method() : string
    {
        return 'sendAudio';
    }

    protected function getRequiredParams() : array
    {
        return [
            'chat_id' => $this->chatId,
            'audio'   => $this->audio,
        ];
    }

    protected function addOptionalParams() : void
    {
        $this->addParam('parse_mode', $this->parsMode);
        $this->addParam('disable_notification', $this->disableNotification);
        $this->addParam('reply_to_message_id', $this->replyToMessageId);
        $this->addParam('reply_markup', $this->replyMarkup);
        $this->addParam('caption', $this->caption);
        $this->addParam('duration', $this->duration);
        $this->addParam('thumb', $this->thumbnail);
        $this->addParam('title', $this->title);
        $this->addParam('performer', $this->performer);

    }

    protected function requiredParams() : array
    {
        return ['chat_id', 'audio'];
    }
}