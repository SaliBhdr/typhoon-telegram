<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendMethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Captionable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasDimensions;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasDuration;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasThumbnail;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasVideo;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Parsable;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Streamable;

class SendVideo extends SendMethodAbstract
{
    use HasVideo,
        Streamable,
        HasReplyMarkUp,
        DisablesNotification,
        Parsable,
        RepliesToMessage,
        Captionable,
        HasDuration,
        HasThumbnail,
        HasDimensions;


    public function method() : string
    {
        return 'sendVideo';
    }

    protected function getRequiredParams() : array
    {
        return [
            'chat_id' => $this->chatId,
            'video'   => $this->video
        ];
    }

    protected function addOptionalParams() : void
    {
        $this->addParam('caption', $this->caption);
        $this->addParam('duration', $this->duration);
        $this->addParam('height', $this->height);
        $this->addParam('width', $this->width);
        $this->addParam('supports_streaming', $this->supportStreaming);
        $this->addParam('parse_mode', $this->parsMode);
        $this->addParam('disable_notification', $this->disableNotification);
        $this->addParam('reply_to_message_id', $this->replyToMessageId);
        $this->addParam('reply_markup', $this->replyMarkup);
    }

    protected function requiredParams() : array
    {
        return ['chat_id', 'video'];
    }

}