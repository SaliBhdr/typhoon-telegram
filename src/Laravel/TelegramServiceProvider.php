<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 1:13 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use SaliBhdr\TyphoonTelegram\Laravel\Commands\InitCommand;
use SaliBhdr\TyphoonTelegram\Laravel\Commands\MakeCommand;
use SaliBhdr\TyphoonTelegram\Laravel\Commands\WebhookCommand;
use SaliBhdr\TyphoonTelegram\Telegram\Api;

class TelegramServiceProvider extends ServiceProvider implements DeferrableProvider
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
        $this->bindMainClass();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerExceptionHandler();

        $this->registerCommands();

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
                $app['config']->get('telegram.bots.default.botToken',null),
                $app['config']->get('telegram.async_requests', false),
                $app['config']->get('telegram.http_client_handler',null)
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
            WebhookCommand::class,
            InitCommand::class,
            MakeCommand::class
        ]);
    }

    protected function addConfig()
    {
        $this->publishes([
            $this->getConfigFile() => config_path('telegram.php')
        ],'config');
    }

    protected function registerExceptionHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \SaliBhdr\TyphoonTelegram\Laravel\Exceptions\TelegramExceptionHandler::class
        );
    }

    public function getConfigFile()
    {
        return __DIR__ . '/../../config/telegram.php';
    }
}