<?php

namespace SaliBhdr\TyphoonTelegram\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use SaliBhdr\TyphoonTelegram\Laravel\Commands\WebHookCommand;
use SaliBhdr\TyphoonTelegram\Telegram\Api;

abstract class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerExceptionHandler();

        $this->registerCommands();

        $this->setupConfig();

        $this->bindMainClass();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $this->addConfig();

        $this->mergeConfigFrom($this->getConfigFile(), 'telegram');
    }

    /**
     * Initialize Telegram Bot SDK Library with Default Config.
     *
     */
    protected function bindMainClass()
    {
        $this->app->singleton(Api::class, function ($app) {

            $telegram = Api::init(
                $app['config']->get('telegram.default_bot_token', null),
                $app['config']->get('telegram.async_requests', false),
                $app['config']->get('telegram.http_client_handler', null)
            );

            // Register Commands
            $telegram->addCommands($app['config']->get('telegram.commands', []));

            // Check if DI needs to be enabled for Commands
            if ($app['config']->get('telegram.inject_command_dependencies', false)) {
                $telegram->setContainer($app);
            }

            return $telegram;
        });

        $this->app->alias(Api::class, 'telegram');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['telegram', Api::class];
    }

    public function registerCommands()
    {
        $this->commands([
            WebHookCommand::class,
        ]);
    }

    protected abstract function addConfig();

    protected abstract function registerExceptionHandler();

    protected function getConfigFile()
    {
        return __DIR__ . '/../../../config/telegram.php';
    }
}
