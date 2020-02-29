<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 1/25/2020
 * Time: 12:20 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram;


use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Update;

abstract class BaseTelegramFlowHandler
{
    /** @var Update $update */
    public $update;

    final public function __construct(Update $update)
    {
        $this->update = $update;

        if(method_exists($this,'boot'))
            $this->boot();

    }

    abstract public function handle();

}
