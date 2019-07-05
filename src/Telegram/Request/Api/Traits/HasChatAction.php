<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:22 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasChatAction
{
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

    public function action(string $action)
    {
        $this->action = $action;

        return $this;
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
}