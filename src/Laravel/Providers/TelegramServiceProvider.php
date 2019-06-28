<?php

namespace SaliBhdr\TyphoonTelegram\Laravel\Providers;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use SaliBhdr\TyphoonTelegram\Laravel\Commands\WebHookCommand;
use SaliBhdr\TyphoonTelegram\Telegram\Api;

/*** Class TelegramServiceProvider.
 */
abstract class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = TRUE;

    /**
     * Holds path to Config File.
     *
     * @var string
     */
    protected $config_filepath;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->registerCommands();
        $this->app->register('SaliBhdr\TyphoonTelegram\Providers\RouteServiceProvider');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerExceptionHandler();

        $this->setupConfig();

        $this->registerApi();
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
    protected function registerApi()
    {
        $this->app->singleton(Api::class, function ($app) {

            $config = $app['config'];

            $telegram = Api::init(
                $config->get('telegram.default_bot_token', FALSE),
                $config->get('telegram.async_requests', FALSE),
                $config->get('telegram.http_client_handler', NULL)
            );

            // Register Commands
            $telegram->addCommands($config->get('telegram.commands', []));

            // Check if DI needs to be enabled for Commands
            if ($config->get('telegram.inject_command_dependencies', FALSE)) {
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
        return __DIR__ . '/../../config/telegram.php';
    }
}
