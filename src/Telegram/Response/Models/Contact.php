<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class Contact.
 *
 *
 * @method string   getPhoneNumber()    Contact's phone number.
 * @method string   getFirstName()      Contact's first name.
 * @method string   getLastName()       (Optional). Contact's last name.
 * @method int      getUserId()         (Optional). Contact's user identifier in Telegram.
 */
class Contact extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}
