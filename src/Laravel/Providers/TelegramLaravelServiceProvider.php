<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 1:13 PM
 */

namespace Salibhdr\TyphoonTelegram\Laravel\Providers;

use Illuminate\Foundation\Application as LaravelApplication;

class TelegramLaravelServiceProvider extends TelegramServiceProvider
{
    /** @var LaravelApplication $app */
    protected $app;

    protected function addConfig()
    {
        $this->publishes([$this->getConfigFile() => config_path('telegram.php')]);
    }

    protected function registerExceptionHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Salibhdr\TyphoonTelegram\Laravel\Exceptions\LaravelExceptionHandler::class
        );
    }
}