<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:50 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\BaseSendInterface;
use Salibhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasChatId;

abstract class SendAbstract extends BaseAbstract implements BaseSendInterface
{

    use HasChatId;

}