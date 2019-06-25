<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Interfaces;


interface SendLocationInterface extends BaseSendMessageInterface
{

    public function latitude(float $latitude);
    public function getLatitude() : ?float;

    public function longitude(float $longitude);
    public function getLongitude() : ?float;

    public function livePeriod(int $seconds);
    public function getLivePeriod() : ?int;

    //optionals ->
    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

    public function replyToMessageId(int $message_id);
    public function getReplyToMessageId() : ?int;

    public function replyMarkup(array $replyMarkup);
    public function getReplyMarkup() : ?string;

}