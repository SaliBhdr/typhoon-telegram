<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Interfaces;

interface ForwardMessageInterface extends BaseSendMessageInterface
{

    public function getFromChatId();
    public function fromChatId($fromChatId);


    public function getMessageId();
    public function messageId($messageId);

    //optionals ->

    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

}