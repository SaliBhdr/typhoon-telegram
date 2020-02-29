<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class Message.
 *
 * @method bool     isOk()
 * @method int      getId()               Unique callback identifier.
 * @method User     getFrom()             Unique callback identifier.
 * @method Message  getMessage()          (Optional). New incoming message of any kind - text, photo, sticker, etc.
 * @method int      getChatInstance()     chat instance id
 * @method string   getData()             chat instance id
 *
 */
class CallBackQuery extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'from' => User::class,
            'message' => Message::class,
        ];
    }


}
