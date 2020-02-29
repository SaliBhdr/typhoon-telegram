<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

/**
 * Class ChatPermissions.
 *
 * @method bool     isOk()
 * @method bool     getCanSendMessages()  	    (Optional). Restricted only. True, if the user is allowed to send text messages, contacts, locations and venues
 * @method bool     getCanSendMediaMessages() 	(Optional). Restricted only. True, if the user is allowed to send audios, documents, photos, videos, video notes and voice notes
 * @method bool     getCanSendPolls()  	        (Optional). Restricted only. True, if the user is allowed to send polls
 * @method bool     getCanSendOtherMessages() 	(Optional). Restricted only. True, if the user is allowed to send animations, games, stickers and use inline bots
 * @method bool     getCanAddWebPagePreviews()  (Optional). Restricted only. True, if the user is allowed to add web page previews to their messages
 * @method bool     getCanChangeInfo()  	    (Optional). Administrators and restricted only. True, if the user is allowed to change the chat title, photo and other settings
 * @method bool     getCanInviteUsers()  	    (Optional). Administrators and restricted only. True, if the user is allowed to invite new users to the chat
 * @method bool     getCanPinMessages()  	    (Optional). Administrators and restricted only. True, if the user is allowed to pin messages; groups and supergroups only
 */
class ChatPermissions extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}
