<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces;

interface ForwardInterface extends BaseSendInterface
{

    public function getFromChatId();
    public function fromChatId($fromChatId);


    public function getMessageId();
    public function messageId($messageId);

    //optionals ->

    public function disableNotification();
    public function isNotificationDisabled() : ?bool;

}