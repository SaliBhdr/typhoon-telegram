<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class ChatMember.
 *
 * @method bool     isOk()
 * @method User     getUser() 	                Information about the user
 * @method string   getStatus()  	            The member's status in the chat. Can be “creator”, “administrator”, “member”, “restricted”, “left” or “kicked”
 * @method string   getCustomTitle()            (Optional). Owner and administrators only. Custom title for this user
 * @method integer  getUntilDate()  	        (Optional). Restricted and kicked only. Date when restrictions will be lifted for this user; unix time
 * @method bool     getCanBeEdited()  	        (Optional). Administrators only. True, if the bot is allowed to edit administrator privileges of that user
 * @method bool     getCanPostMessages()  	    (Optional). Administrators only. True, if the administrator can post in the channel; channels only
 * @method bool     getCanEditMessages()  	    (Optional). Administrators only. True, if the administrator can edit messages of other users and can pin messages; channels only
 * @method bool     getCanDeleteMessages()  	(Optional). Administrators only. True, if the administrator can delete messages of other users
 * @method bool     getCanRestrictMembers()  	(Optional). Administrators only. True, if the administrator can restrict, ban or unban chat members
 * @method bool     getCanPromoteMembers()  	(Optional). Administrators only. True, if the administrator can add new administrators with a subset of his own privileges or demote administrators that he has promoted, directly or indirectly (promoted by administrators that were appointed by the user)
 * @method bool     getCanChangeInfo()  	    (Optional). Administrators and restricted only. True, if the user is allowed to change the chat title, photo and other settings
 * @method bool     getCanInviteUsers()  	    (Optional). Administrators and restricted only. True, if the user is allowed to invite new users to the chat
 * @method bool     getCanPinMessages()  	    (Optional). Administrators and restricted only. True, if the user is allowed to pin messages; groups and supergroups only
 * @method bool     getIsMember()  	            (Optional). Restricted only. True, if the user is a member of the chat at the moment of the request
 * @method bool     getCanSendMessages()  	    (Optional). Restricted only. True, if the user is allowed to send text messages, contacts, locations and venues
 * @method bool     getCanSendMediaMessages() 	(Optional). Restricted only. True, if the user is allowed to send audios, documents, photos, videos, video notes and voice notes
 * @method bool     getCanSendPolls()  	        (Optional). Restricted only. True, if the user is allowed to send polls
 * @method bool     getCanSendOtherMessages() 	(Optional). Restricted only. True, if the user is allowed to send animations, games, stickers and use inline bots
 * @method bool     getCanAddWebPagePreviews()  (Optional). Restricted only. True, if the user is allowed to add web page previews to their messages
 */
class ChatMember extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'user' => User::class,
        ];
    }

    /**
     * check if chat member is creator of chat
     * @return bool
     */
    public function isCreator()
    {
        return $this->isStatus('creator');
    }

    /**
     * check if chat member is administrator of chat
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->isStatus('administrator');
    }

    /**
     * check if chat member is member of chat
     * @return bool
     */
    public function isMember()
    {
        return $this->isStatus('member');
    }

    /**
     * check if chat member is restricted member of chat
     * @return bool
     */
    public function isRestricted()
    {
        return $this->isStatus('restricted');
    }

    /**
     * check if chat member is left member of chat
     * @return bool
     */
    public function isLeft()
    {
        return $this->isStatus('left');
    }

    /**
     * check if chat member is kicked member of chat
     * @return bool
     */
    public function isKicked()
    {
        return $this->isStatus('kicked');
    }

    /**
     * check status of chat member
     * @param $status
     * @return bool
     */
    public function isStatus($status)
    {
        return $this->getStatus() == $status;
    }
}
