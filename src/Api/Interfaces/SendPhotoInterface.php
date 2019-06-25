<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Interfaces;


interface SendPhotoInterface extends BaseSendMessageInterface
{

    public function photo($photo);
    public function getPhoto();

    //optionals ->
    public function caption(string $caption);
    public function getCaption() : ?string;

    public function htmlParsMode();
    public function markdownParseMode();
    public function getParsMode() : ?string;

    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

    public function replyToMessageId(int $message_id);
    public function getReplyToMessageId() : ?int;

    public function replyMarkup(array $replyMarkup);
    public function getReplyMarkup() : ?string;

}