<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Interfaces\ForwardMessageInterface;
use Salibhdr\TyphoonTelegram\Api\Traits\DisablesNotification;
use Salibhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;

class FrowardMessage extends SendAbstract implements ForwardMessageInterface
{
    use DisablesNotification;

    protected $fromChatId;

    protected $messageId;


    public function fromChatId($fromChatId): FrowardMessage
    {
        $this->fromChatId = $fromChatId;

        return $this;
    }

    /**    * @return mixed
     */
    public function getFromChatId()
    {
        return $this->fromChatId;
    }

    /**    * @param mixed $messageId
     * @return FrowardMessage
     */
    public function messageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**    * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    protected function addParams() :void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'from_chat_id' => $this->getFromChatId(),
            'message_id' => $this->getMessageId(),
        ];
    }

    protected function addOptionalParams():void
    {
        if (!is_null($this->isNotificationDisabled())) {
            $this->params['disable_notification'] = $this->isNotificationDisabled();
        }
    }


    public function sendMethod() : string
    {
        return 'forwardMessage';
    }

    protected function requiredParams(): array
    {
        return ['chat_id','from_chat_id','message_id'];
    }

}