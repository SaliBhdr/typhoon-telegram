<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:50 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts;

use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasChatId;

/**
 * extend from SendMethodAbstract if the api method has chat_id
 *
 * Class SendMethodAbstract
 * @package SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts
 */
abstract class SendMethodAbstract extends MethodAbstract
{
    use HasChatId;
}