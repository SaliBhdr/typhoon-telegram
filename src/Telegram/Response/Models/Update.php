<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

use SaliBhdr\TyphoonTelegram\Laravel\Facades\Telegram;/**
 * Class Update.
 * @method bool            isOk()
 * @method int             getUpdateId()         The update's unique identifier. Update identifiers start from a certain positivenumber and increase sequentially.
 * @method Message         getMessage()          (Optional). New incoming message of any kind - text, photo, sticker, etc.
 * @method CallBackQuery   getCallbackQuery()    (Optional). New incoming callback query of any kind - text, photo,sticker, etc.
 */
class Update extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'message' => Message::class,
            'callback_query' => CallBackQuery::class,
        ];
    }

    /**
     * Get recent message.
     *
     * @return Update
     */
    public function recentMessage()
    {
        return new static($this->last());
    }

    /**
     * checks if webhook update is a message
     *
     * @return bool
     */
    public function isMessage()
    {
        return $this->has('message');
    }

    /**
     * checks if webhook update is a callback_query
     *
     * @return bool
     */
    public function isCallbackQuery()
    {
        return $this->has('callback_query');
    }

    /**
     * checks if webhook update is a inline callback_query
     *
     * @return bool
     */
    public function isInlineCallbackQuery()
    {
        if ($this->isCallbackQuery())
            return isset($this->callback_query->inline_message_id);

        return false;
    }

    /**
     * checks if webhook update is a inline_query
     *
     * @return bool
     */
    public function isInlineQuery()
    {
        return $this->has('inline_query');
    }

    /**
     * @return string
     *
     * message types : ['audio', 'document', 'photo', 'sticker', 'video', 'voice', 'contact', 'location', 'text']
     */
    public function getMessageType()
    {
        return Telegram::detectMessageType($this);
    }

    /**
     * @param string $type
     * message types : ['audio', 'document', 'photo', 'sticker', 'video', 'voice', 'contact', 'location', 'text']
     * @return bool
     */
    public function isType(string $type)
    {
        $messageType = $this->getMessageType();

        return $messageType == $type;
    }
}
