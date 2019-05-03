<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Interfaces\SendChatActionInterface;
use Salibhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;
use Salibhdr\TyphoonTelegram\Exceptions\InvalidChatActionException;

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

    protected $chatActions = [
        'typing',
        'upload_photo',
        'record_video',
        'upload_video',
        'record_audio',
        'upload_audio',
        'upload_document',
        'find_location',
        'record_video_note',
        'upload_video_note '
    ];

    protected function addOptionalParams(): void
    {
    }

    public function method(): string
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

    public function isTyping()
    {
        $this->action('typing');

        return $this;
    }

    public function uploadPhoto()
    {
        $this->action('upload_photo');

        return $this;
    }

    public function uploadVideo()
    {
        $this->action('upload_video');

        return $this;
    }

    public function uploadAudio()
    {
        $this->action('upload_audio');

        return $this;
    }

    public function uploadDocument()
    {
        $this->action('upload_document');

        return $this;
    }

    public function recordVideo()
    {
        $this->action('record_video');

        return $this;
    }

    public function recordAudio()
    {
        $this->action('record_audio');

        return $this;
    }

    public function findLocation()
    {
        $this->action('find_location');

        return $this;
    }

    public function recordVideoNote()
    {
        $this->action('record_video_note');

        return $this;
    }

    public function uploadVideoNote()
    {
        $this->action('upload_video_note');

        return $this;
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