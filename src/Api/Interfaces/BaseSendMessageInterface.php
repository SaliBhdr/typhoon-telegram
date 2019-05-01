<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:47 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Interfaces;


interface BaseSendMessageInterface
{
    public function chatId($chat_id);

    public function getChatId();

    public function setParams(array $params = []);

    public function getParams() : array;

    public function sendMethod(): string;

}