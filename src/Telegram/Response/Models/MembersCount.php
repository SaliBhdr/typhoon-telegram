<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class MembersCount.
 *
 * @method bool isOk()
 * @method int getCount() return number of members in chat
 */
class MembersCount extends BaseModel
{
    public function __construct($data)
    {
        parent::__construct($data);

        $count = $this->items[0] ?? 0;

        $this->items = [];

        $this->items['count'] = $count;
    }

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }

}
