<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Interfaces;


interface SendChatActionInterface extends BaseSendMessageInterface
{

   public function action(string $action);
   public function getAction() : ?string;

}