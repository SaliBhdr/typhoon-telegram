<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

use SaliBhdr\TyphoonTelegram\Laravel\Facades\Telegram;

/**
 * Class Message.
 * @method bool isOk()
 * @method int              getMessageId()              Unique message identifier.
 * @method User             getFrom()                   (Optional). Sender, can be empty for messages sent to channels.
 * @method int              getDate()                   Date the message was sent in Unix time.
 * @method Chat             getChat()                   Conversation the message belongs to.
 * @method User             getForwardFrom()            (Optional). For forwarded messages, sender of the originalmessage.
 * @method Chat             getForwardFromChat()        (Optional). For messages forwarded from channels, information about the original channel
 * @method int              getForwardDate()            (Optional). For forwarded messages, date the original messagewas sent in Unix time.
 * @method Message          getReplyToMessage()         (Optional). For replies, the original message. Note that theMessage object in this field will not contain further reply_to_message fields even if it itself is a reply.
 * @method string           getText()                   (Optional). For text messages, the actual UTF-8 text of themessage.
 * @method Audio            getAudio()                  (Optional). Message is an audio file, information about thefile.
 * @method Document         getDocument()               (Optional). Message is a general file, information about thefile.
 * @method PhotoSize[]      getPhoto()                  (Optional). Message is a photo, available sizes of the photo.
 * @method Sticker          getSticker()                (Optional). Message is a sticker, information about thesticker.
 * @method Video            getVideo()                  (Optional). Message is a video, information about the video.
 * @method Voice            getVoice()                  (Optional). Message is a voice message, information about thefile.
 * @method string           getCaption()                (Optional). Caption for the photo or video contact.
 * @method Contact          getContact()                (Optional). Message is a shared contact, information about thecontact.
 * @method Location         getLocation()               (Optional). Message is a shared location, information about thelocation.
 * @method User             getNewChatParticipant()     (Optional). A new member was added to the group, informationabout them (this member may be the bot itself).
 * @method User             getLeftChatParticipant()    (Optional). A member was removed from the group, informationabout them (this member may be the bot itself).
 * @method string           getNewChatTitle()           (Optional). A chat title was changed to this value.
 * @method PhotoSize[]      getNewChatPhoto()           (Optional). A chat photo was change to this value.
 * @method bool             getDeleteChatPhoto()        (Optional). Service message: the chat photo was deleted.
 * @method bool             getGroupChatCreated()       (Optional). Service message: the group has been created.
 * @method bool             getSupergroupChatCreated()  (Optional). Service message: the super group has been created.
 * @method bool             getChannelChatCreated()     (Optional). Service message: the channel has been created.
 * @method int              getMigrateToChatId()        (Optional). The group has been migrated to a supergroup withthe specified identifier, not exceeding 1e13 by absolute value.
 * @method int              getMigrateFromChatId()      (Optional). The supergroup has been migrated from a group withthe specified identifier, not exceeding 1e13 by absolute value.
 * @method object
 * @method Entity getEntities()
 */
class Message extends BaseModel
{
    /** @var string $incomingText this is message text or caption if text not exists */
    protected $incomingText;

    public function __construct($data)
    {
        parent::__construct($data);

        $this->setIncomingText();
    }

    /**
     * sets incoming text
     */
    private function setIncomingText()
    {
        if (is_null($this->incomingText)) {
            if ($this->has('text'))
                $this->incomingText = $this->getText();
            elseif ($this->has('caption'))
                $this->incomingText = $this->getCaption();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'chat'                  => Chat::class,
            'from'                  => User::class,
            'entities'              => Entity::class,
            'forward_from'          => User::class,
            'forward_from_chat'     => Chat::class,
            'reply_to_message'      => self::class,
            'audio'                 => Audio::class,
            'document'              => Document::class,
            'photo'                 => PhotoSize::class,
            'sticker'               => Sticker::class,
            'video'                 => Video::class,
            'voice'                 => Voice::class,
            'contact'               => Contact::class,
            'location'              => Location::class,
            'new_chat_participant'  => User::class,
            'left_chat_participant' => User::class,
            'new_chat_photo'        => PhotoSize::class,
        ];
    }

    /**
     * check if message type is bot command
     *
     * @return bool
     */
    public function isCommand()
    {
        return $this->has('entities')
               && !empty($this->entities[0]['type'])
               && $this->entities[0]['type'] == 'bot_command';
    }

    /**
     * checks if incoming text is equal to input text
     *
     * @param $text
     *
     * @return bool
     */
    public function textIs($text)
    {
        return $this->incomingText == $text;
    }

    /**
     * checks if incoming text is equal to input text in given offset
     *
     * @param $text
     *
     * @return bool
     */
    public function textPos($text)
    {
        return strpos($this->incomingText, $text) !== false;
    }

    /**
     * @param $pattern
     *
     * @return array
     */
    public function textPregMatch($pattern)
    {
        preg_match_all($pattern, $this->incomingText, $match);

        return $match;
    }

    /**
     * @return mixed
     */
    public function getIncomingText()
    {
        return $this->incomingText;
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
     *
     * @return bool
     */
    public function isType(string $type)
    {
        return $this->getMessageType() == $type;
    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isAudio()
    {
        return $this->isType('audio');
    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isDocument()
    {
        return $this->isType('document');

    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isPhoto()
    {
        return $this->isType('photo');

    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isSticker()
    {
        return $this->isType('sticker');

    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isVideo()
    {
        return $this->isType('video');

    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isVoice()
    {
        return $this->isType('voice');

    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isContact()
    {
        return $this->isType('contact');

    }

    /**
     * detects type of message
     *
     * @return bool
     */
    public function isText()
    {
        return $this->isType('text');
    }
}
