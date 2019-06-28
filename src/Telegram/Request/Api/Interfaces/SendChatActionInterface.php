<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces;


interface SendChatActionInterface extends BaseSendInterface
{

   public function action(string $action);
   public function getAction() : ?string;

}