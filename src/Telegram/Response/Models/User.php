<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class User.
 *
 * @method bool isOk()
 * @method int      getId()         Unique identifier for this user or bot.
 * @method string   getFirstName()  User's or bot's first name.
 * @method string   getLastName()   (Optional). User's or bot's last name.
 * @method string   getUsername()   (Optional). User's or bot's username.
 */
class User extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }

    /**
     * (extra). full name of user if last name exists otherwise returns first name
     * @return string
     */
    public function getFullName()
    {
        $fullName = $this->getFirstName();

        if ($this->has('last_name'))
            $fullName .= ' '.$this->getLastName();

        return $fullName;
    }
}
