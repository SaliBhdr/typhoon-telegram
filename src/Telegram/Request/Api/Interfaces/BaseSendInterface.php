<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:47 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces;


interface BaseSendInterface extends BaseInterface
{
    public function chatId($chat_id);

    public function getChatId();

}