<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces;


interface SendAudioInterface extends BaseSendMessageInterface
{

    public function audio($audio);
    public function getAudio();

    //optionals ->
    public function caption(string $caption);
    public function getCaption() : ?string;

    public function htmlParsMode();
    public function markdownParseMode();
    public function getParsMode() : ?string;

    public function duration(int $seconds);
    public function getDuration() : ?int;

    public function performer(string $performer);
    public function getPerformer() : ?string;

    public function title(string $trackName);
    public function getTitle() : ?string;

    public function thumb($thumbnail);
    public function getThumb();

    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

    public function replyToMessageId(int $message_id);
    public function getReplyToMessageId() : ?int;

    public function replyMarkup(array $replyMarkup);
    public function getReplyMarkup() : ?string;

}