<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Interfaces\SendChatActionInterface;
use Salibhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;

class SendChatAction extends SendAbstract implements SendChatActionInterface
{

    protected function addParams(): void
    {
        $this->params = [
            'chat_id' => $this->getChatId(),
            'action' => $this->getAction()
        ];
    }

    protected $action;

    protected function addOptionalParams(): void {}

    public function sendMethod(): string
    {
        return 'sendChatAction';
    }

    protected function requiredParams(): array
    {
        return ['chat_id', 'action'];
    }


    public function action(string $action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): ?string
    {
      return $this->action;
    }
}