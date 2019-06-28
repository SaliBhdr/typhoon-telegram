<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 1:13 PM
 */

namespace Salibhdr\TyphoonTelegram\Laravel\Providers;

use Laravel\Lumen\Application as LumenApplication;

class TelegramLumenServiceProvider extends TelegramServiceProvider
{
    /** @var  LumenApplication $app*/
    protected $app;

    protected function addConfig()
    {
        $this->app->configure('telegram');
    }

    protected function registerExceptionHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Salibhdr\TyphoonTelegram\Laravel\Exceptions\LumenExceptionHandler::class
        );
    }
}