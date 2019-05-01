<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Interfaces;


interface SendDocumentInterface extends BaseSendMessageInterface
{

    public function document($document);
    public function getDocument();

    //optionals ->
    public function caption(string $caption);
    public function getCaption() : ?string;

    public function htmlParsMode();
    public function markdownParseMode();
    public function getParsMode() : ?string;

    public function thumb($thumbnail);
    public function getThumb();

    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

    public function replyToMessageId(int $message_id);
    public function getReplyToMessageId() : ?int;

    public function replyMarkup(array $replyMarkup);
    public function getReplyMarkup() : ?string;

}