<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:47 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Interfaces;


interface BaseSendMessageInterface extends BaseInterface
{
    public function chatId($chat_id);

    public function getChatId();

}