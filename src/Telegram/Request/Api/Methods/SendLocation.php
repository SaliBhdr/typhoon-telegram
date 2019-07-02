<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramLocationLivePeriodException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendMethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasLocation;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendLocation extends SendMethodAbstract
{

    use HasLocation,
        DisablesNotification,
        RepliesToMessage,
        HasReplyMarkUp;

    protected const minLivePeriod = 60;

    protected const maxLivePeriod = 86400;

    public function method() : string
    {
        return 'sendLocation';
    }

    protected function getRequiredParams() : array
    {
        return [
            'chat_id'   => $this->chatId,
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    protected function addOptionalParams() : void
    {
        $this->addParam('live_period', $this->livePeriod);
        $this->addParam('disable_notification', $this->disableNotification);
        $this->addParam('reply_to_message_id', $this->replyToMessageId);
        $this->addParam('reply_markup', $this->replyMarkup);

    }

    protected function requiredParams() : array
    {
        return ['chat_id', 'latitude', 'longitude'];
    }

    /**
     * @throws TelegramLocationLivePeriodException
     */
    protected function extraValidation()
    {
        if (!is_null($this->livePeriod) && !($this->livePeriod >= static::minLivePeriod && $this->livePeriod <= static::minLivePeriod))
            throw new TelegramLocationLivePeriodException(static::minLivePeriod, static::minLivePeriod);
    }
}