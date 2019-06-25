<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Interfaces;


interface SendAnimationInterface extends BaseSendMessageInterface
{

    public function animation($animation);
    public function getAnimation();

    //optionals ->
    public function caption(string $caption);
    public function getCaption() : ?string;

    public function htmlParsMode();
    public function markdownParseMode();
    public function getParsMode() : ?string;

    public function duration(int $seconds);
    public function getDuration() : ?int;

    public function width(int $pixels);
    public function getWidth() : ?int;

    public function height(int $pixels);
    public function getHeight() : ?int;

    public function thumb($thumbnail);
    public function getThumb();

    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

    public function replyToMessageId(int $message_id);
    public function getReplyToMessageId() : ?int;

    public function replyMarkup(array $replyMarkup);
    public function getReplyMarkup() : ?string;

}