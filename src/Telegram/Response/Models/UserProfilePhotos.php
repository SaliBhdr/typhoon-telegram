<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class UserProfilePhotos.
 *
 * @method bool isOk()
 * @method int          getTotalCount()     Total number of profile pictures the target user has.
 * @method PhotoSize[]  getPhotos()         Requested profile pictures (in up to 4 sizes each).
 */
class UserProfilePhotos extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'photos' => PhotoSize::class,
        ];
    }
}
