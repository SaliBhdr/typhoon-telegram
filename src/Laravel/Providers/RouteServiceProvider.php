<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/25/2019
 * Time: 5:40 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (method_exists($this->app, 'routesAreCached')) {
            if (!$this->app->routesAreCached())
                $this->setRoutes();
        } else {
            $this->setRoutes();
        }
    }

    private function setRoutes()
    {
        if (!$this->isAutoRoutesActive())
            return;

        $bots = config('telegram.bots');

        if (empty($bots))
            return;

        $this->registerBotWebhooks($bots);

    }

    private function registerBotWebhooks($bots)
    {
        foreach ($bots as $bot) {

            if ($bot['is_active']) {
                $this->registerBotRoute($bot);
            }
        }
    }

    private function registerBotRoute($bot)
    {
        $this->app['router']->post(
            "{$bot['botToken']}/webhook",
            "App\Http\Controllers\\" . "{$bot['controller']}");
    }

    private function isAutoRoutesActive()
    {
        return config('telegram.automatic-routes') ?? false;
    }
}