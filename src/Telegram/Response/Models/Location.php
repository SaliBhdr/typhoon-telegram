<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class Location.
 *
 *
 * @method float    getLongitude()  Longitude as defined by sender.
 * @method float    getLatitude()   Latitude as defined by sender.
 */
class Location extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}
