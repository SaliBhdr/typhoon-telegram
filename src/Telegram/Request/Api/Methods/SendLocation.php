<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\SendLocationInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\DisablesNotification;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasReplyMarkUp;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\RepliesToMessage;

class SendLocation extends SendAbstract implements SendLocationInterface
{

    use DisablesNotification,
        RepliesToMessage,
        HasReplyMarkUp;

    protected $latitude;

    protected $longitude;

    protected $livePeriod;

    protected const minLivePeriod = 60;

    protected const maxLivePeriod = 86400;

    public function method(): string
    {
        return 'sendLocation';
    }

    protected function addParams(): void
    {
        $this->params = [
            'chat_id'   => $this->getChatId(),
            'latitude'  => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
        ];
    }

    protected function addOptionalParams(): void
    {
        if (!is_null($this->isNotificationDisabled())) {
            $this->params['disable_notification'] = $this->isNotificationDisabled();
        }

        if (!is_null($this->getReplyToMessageId())) {
            $this->params['reply_to_message_id'] = $this->getReplyToMessageId();
        }

        if (!is_null($this->getReplyMarkup())) {
            $this->params['reply_markup'] = $this->getReplyMarkup();
        }

        if (!is_null($this->getReplyMarkup())) {
            $this->params['reply_markup'] = $this->getReplyMarkup();
        }

        if (!is_null($this->getLivePeriod())) {
            $this->params['live_period'] = $this->getLivePeriod();
        }
    }

    protected function requiredParams(): array
    {
        return ['chat_id', 'latitude', 'longitude'];
    }

    public function latitude(float $latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function longitude(float $longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }


    public function getLivePeriod(): ?int
    {
        return $this->livePeriod;
    }

    /**
     * @param mixed $livePeriod
     * @return SendLocation
     */
    public function livePeriod(int $livePeriod)
    {
        if ($livePeriod >= static::minLivePeriod && $livePeriod <= static::maxLivePeriod)
            $this->livePeriod = $livePeriod;

        return $this;
    }

    /**
     * @throws LivePeriodException
     */
    protected function extraValidation()
    {
        if (!is_null($this->livePeriod) && !($this->livePeriod >= static::minLivePeriod && $this->livePeriod <= static::minLivePeriod))
            throw new LivePeriodException(static::minLivePeriod, static::minLivePeriod);
    }
}