<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;


use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramProfilePhotoRangeLimitException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\MethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasOffset;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasUserId;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\Limitable;


class GetUserProfilePhotos extends MethodAbstract
{

    use HasUserId,
        Limitable,
        HasOffset;

    protected const minLimit = 1;

    protected const maxLimit = 100;

    public function method() : string
    {
        return 'getUserProfilePhotos';
    }

    protected function getRequiredParams() : array
    {
        return [
            'user_id' => $this->userId,
        ];
    }

    protected function addOptionalParams() : void
    {
        $this->addParam('limit', $this->limit);
        $this->addParam('offset', $this->offset);
    }

    protected function requiredParams() : array
    {
        return ['user_id'];
    }

    /**
     * @throws TelegramProfilePhotoRangeLimitException
     */
    protected function extraValidation()
    {
        if (!is_null($this->limit) && !($this->limit >= static::minLimit && $this->limit <= static::maxLimit))
            throw new TelegramProfilePhotoRangeLimitException(static::minLimit, static::maxLimit);
    }

}