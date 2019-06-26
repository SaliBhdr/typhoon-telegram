<?php

namespace SaliBhdr\TyphoonTelegram\Laravel\Providers;

use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use SaliBhdr\TyphoonTelegram\Telegram\Api;
use SaliBhdr\TyphoonTelegram\Laravel\Commands\WebHookCommand;

/*** Class TelegramServiceProvider.
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**    * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**    * Holds path to Config File.
     *
     * @var string
     */
    protected $config_filepath;

    /**    * Bootstrap the application events.
     */
    public function boot()
    {
        $this->registerCommands();
        $this->app->register('SaliBhdr\TyphoonTelegram\Providers\RouteServiceProvider');
    }


    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    protected function setupConfig(Application $app)
    {
        $source = $this->getConfigFile();

        $this->addConfig($source, $app);

        $this->mergeConfigFrom($source, 'telegram');
    }

    /**    * Register the service provider.
     */
    public function register()
    {
        $this->setupConfig($this->app);

        $this->registerTelegram($this->app);
    }

    /**    * Initialize Telegram Bot SDK Library with Default Config.
     *
     * @param Application $app
     */
    protected function registerTelegram(Application $app)
    {
        $app->singleton(Api::class, function ($app) {

            $config = $app['config'];

            $telegram = Api::init(
                $config->get('telegram.default_bot_token', false),
                $config->get('telegram.async_requests', false),
                $config->get('telegram.http_client_handler', null)
            );

            // Register Commands
            $telegram->addCommands($config->get('telegram.commands', []));

            // Check if DI needs to be enabled for Commands
            if ($config->get('telegram.inject_command_dependencies', false)) {
                $telegram->setContainer($app);
            }

            return $telegram;
        });

        $app->alias(Api::class, 'telegram');
    }

    /**    * Get the services provided by the provider.
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

    private function addConfig($source, Application $app)
    {
        if (class_exists('Illuminate\Foundation\Application') && $app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => config_path('telegram.php')]);
        } elseif (class_exists('Laravel\Lumen\Application') && $app instanceof LumenApplication) {
            $app->configure('telegram');
        }
    }

    private function getConfigFile()
    {
        return __DIR__ . '/../../config/telegram.php';
    }


}
