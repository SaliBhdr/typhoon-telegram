<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces;


interface SendInterface extends BaseSendInterface
{

    public function text($text);

    public function getText() : ?string;

    //optionals ->
    public function htmlParsMode();
    public function markdownParseMode();
    public function getParsMode() : ?string;

    public function disableWebPagePreview();
    public function isWebPagePreviewDisabled() : ?bool;


    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

    public function replyToMessageId(int $message_id);
    public function getReplyToMessageId() : ?int;


    public function replyMarkup(array $replyMarkup);
    public function getReplyMarkup() : ?string;

}