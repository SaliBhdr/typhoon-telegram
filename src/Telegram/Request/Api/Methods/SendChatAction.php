<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;


use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\InvalidChatActionException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendMethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasChatAction;

class SendChatAction extends SendMethodAbstract
{

    use HasChatAction;

    public function method() : string
    {
        return 'sendChatAction';
    }

    protected function getRequiredParams() : array
    {
        return [
            'chat_id' => $this->chatId,
            'action'  => $this->action
        ];
    }


    protected function addOptionalParams() : void
    {
        return;
    }

    protected function requiredParams() : array
    {
        return ['chat_id', 'action'];
    }

    /**
     * @throws InvalidChatActionException
     */
    protected function extraValidation()
    {
        if (!isset($this->params['action']) && in_array($this->params['action'], $this->chatActions)) {
            throw new InvalidChatActionException($this->chatActions);
        }
    }
}