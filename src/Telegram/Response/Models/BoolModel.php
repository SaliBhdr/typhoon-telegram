<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;


/**
 * Class DeletedMessage
 *
 * @method bool     isOk()
 */
class BoolModel extends BaseModel
{
    /**
     * @inheritDoc
     */
    public function relations()
    {
        return [];
    }

    /**
     * check if the message is deleted
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->items[0] ?? false;
    }

}
