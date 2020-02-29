<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class ChatAdministrators.
 *
 * @method bool isOk()
 */
class ChatAdministrators extends BaseModel
{
    protected $chatMembers = [];

    public function __construct($data)
    {
        parent::__construct($data);

        $this->mapChatMembersCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }

    /**
     * returns an Array of ChatMember objects that contains information about all chat administrators except other bots.
     * If the chat is a group or a supergroup and no administrators were appointed, only the creator will be returned.
     */
    public function getChatMembers()
    {
        return $this->all();
    }

    /**
     * maps result of api into chat member collection
     */
    protected function mapChatMembersCollection()
    {
        $this->map(function ($item) {
            return new ChatMember($item);
        });
    }
}
