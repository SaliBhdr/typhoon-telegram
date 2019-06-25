<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Methods;

use SaliBhdr\TyphoonTelegram\Api\Abstracts\GetAbstract;
use SaliBhdr\TyphoonTelegram\Api\Interfaces\GetUserProfilePhotosInterface;
use SaliBhdr\TyphoonTelegram\Exceptions\ProfilePhotoLimitRangeException;


class GetUserProfilePhotos extends GetAbstract implements GetUserProfilePhotosInterface
{

    protected $userId;

    protected $limit;

    protected $offset;

    protected const minLimit = 1;

    protected const maxLimit = 100;

    public function method(): string
    {
       return 'getUserProfilePhotos';
    }

    protected function addParams(): void
    {
        $this->params = [
            'user_id' => $this->getUserId(),
        ];
    }

    protected function addOptionalParams(): void
    {
        if (!is_null($this->getLimit())) {
            $this->params['limit'] = $this->getLimit();
        }

        if (!is_null($this->getOffset())) {
            $this->params['offset'] = $this->getOffset();
        }
    }

    protected function requiredParams(): array
    {
        return ['user_id'];
    }


    public function offset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function limit(int $limit)
    {
        if ($limit >= static::minLimit && $limit <= static::maxLimit)
            $this->limit = $limit;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function userId(int $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @throws ProfilePhotoLimitRangeException
     */
    protected function extraValidation()
    {
        if (!is_null($this->limit) && !($this->limit >= static::minLimit && $this->limit <= static::maxLimit))
            throw new ProfilePhotoLimitRangeException(static::minLimit, static::maxLimit);
    }
}