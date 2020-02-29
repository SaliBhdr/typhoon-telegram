<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class Chat.
 *
 * @method bool            isOk()
 * @method int             getId()                   Unique identifier for this chat, not exceeding 1e13 by absolute value.
 * @method string          getType()                 Type of chat, can be either 'private', 'group', 'supergroup' or 'channel'.
 * @method string          getTitle()                (Optional). Title, for channels and group chats.
 * @method string          getUsername()             (Optional). Username, for private chats and channels if available
 * @method string          getFirstName()            (Optional). First name of the other party in a private chat
 * @method string          getLastName()             (Optional). Last name of the other party in a private chat
 * @method ChatPhoto       getPhoto()                (Optional). Chat photo. Returned only in getChat().
 * @method string          description()             (Optional). Description, for groups, supergroups and channel chats. Returned only in getChat().
 * @method string          getInviteLink()           (Optional). Chat invite link, for groups, supergroups and channel chats. Each administrator in a chat generates their own invite links, so the bot must first generate the link using exportChatInviteLink. Returned only in getChat().
 * @method Message         getPinnedMessage()        (Optional). Pinned message, for groups, supergroups and channels. Returned only in getChat().
 * @method ChatPermissions getPermissions()          (Optional). Default chat member permissions, for groups and supergroups. Returned only in getChat().
 * @method Integer         getSlowModeDelay()        (Optional). For supergroups, the minimum allowed delay between consecutive messages sent by each unpriviledged user. Returned only in getChat().
 * @method string          getStickerSetName()       (Optional). For supergroups, name of group sticker set. Returned only in getChat().
 * @method Boolean         getCanSetStickerSet()     (Optional). True, if the bot can change the group sticker set. Returned only in getChat().
 */
class Chat extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'pinned_message' => Message::class,
            'photo'          => ChatPhoto::class,
            'permissions'    => ChatPermissions::class,
        ];
    }

    /**
     * checks if chat type is private
     *
     * @return bool
     */
    public function isPrivate()
    {
        return $this->isType('private');
    }

    /**
     * checks if chat type is group
     *
     * @return bool
     */
    public function isGroup()
    {
        return $this->isType('group');
    }

    /**
     * checks if chat type is supergroup
     *
     * @return bool
     */
    public function isSuperGroup()
    {
        return $this->isType('supergroup');
    }

    /**
     * checks if chat type is channel
     *
     * @return bool
     */
    public function isChannel()
    {
        return $this->isType('channel');
    }

    /**
     * checks chat type
     * @param $type
     * @return bool
     */
    protected function isType($type)
    {
        return $this->getType() == $type;
    }
}
