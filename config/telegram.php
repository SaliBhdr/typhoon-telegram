<?php

use GuzzleHttp\RequestOptions as GuzzleOption;

return [
    'automatic-routes' => true,

    'debug' => env('TELEGRAM_DEBUG', config('app.debug')),

    'env' => env('TELEGRAM_ENV', config('app.env')), #local || production
    /*
    |--------------------------------------------------------------------------
    | Asynchronous Requests [Optional]
    |--------------------------------------------------------------------------
    |
    | When set to True, All the requests would be made non-blocking (Async).
    |
    | Default: false
    | Possible Values: (Boolean) "true" OR "false"
    |
    */
    'async_requests' => env('TELEGRAM_ASYNC_REQUESTS', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Handler [Optional]
    |--------------------------------------------------------------------------
    |
    | If you'd like to use a custom HTTP Client Handler.
    | Should be an instance of \SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\HttpClientInterface
    |
    | Default: GuzzlePHP
    |
    */
    'http_client_handler' => \SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\GuzzleHttpClient::class,


    /*--------------------------------------------------------------------------
     | Guzzle options
     |--------------------------------------------------------------------------
     | You can set all guzzle Options of guzzle here
     |
     | @see http://docs.guzzlephp.org/en/stable/request-options.html
     |
     */
    'guzzle' => [
        GuzzleOption::TIMEOUT => 60,
        GuzzleOption::CONNECT_TIMEOUT => 20,
        GuzzleOption::VERIFY => false,
        GuzzleOption::HTTP_ERRORS => env('TELEGRAM_GUZZlE_ERROR', config('app.debug')),
        GuzzleOption::PROXY
        /*
        GuzzleOption::PROXY => [
            //You can provide proxy URLs that contain a scheme, username, and password. For example, "http://username:password@192.168.16.1:10"
            'http'  => 'tcp://localhost:8125', // Use this proxy with "http"
            'https' => 'tcp://localhost:9124', // Use this proxy with "https",
            'no' => ['.mit.edu', 'foo.com']    // Don't use a proxy with these
        ],
        */
    ],


    /*
    |--------------------------------------------------------------------------
    | Register Telegram Commands [Optional]
    |--------------------------------------------------------------------------
    |
    | If you'd like to use the SDK's built in command handler system,
    | You can register all the commands here.
    |
    | The command class should extend the \SaliBhdr\TyphoonTelegram\Commands\Command class.
    |
    | Default: The SDK registers, a help command which when a user sends /help
    | will respond with a list of available commands and description.
    |
    */
    'handle_commands' => true,

    'commands' => [
        App\Telegram\Commands\HelpCommand::class,
        App\Telegram\Commands\StartCommand::class,
    ],

    'handlers' => [
        'boot' => App\Telegram\Handlers\BootHandler::class,
        'message' => App\Telegram\Handlers\MessageFlowHandler::class,
        'callback_query' => App\Telegram\Handlers\CallBackFlowHandler::class,
        'inline_callback' => App\Telegram\Handlers\InlineCallBackFlowHandler::class,
        'inline_query' => App\Telegram\Handlers\InlineQueryFlowHandler::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot API Access Token [REQUIRED]
    |--------------------------------------------------------------------------
    |
    | Your Telegram's Bot Access Token.
    | Example: 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
    |
    | Refer for more details:
    | https://core.telegram.org/bots#botfather
    |
    */
    'default_bot' => 'main',

    'bots' => [
        'main' => [
            'is_active' => true,
            'baseUrl' => env('TELEGRAM_APP_URL', config('app.url')),
            'botToken' => env('TELEGRAM_DEFAULT_BOT_TOKEN', 'YOUR-BOT-TOKEN'),
            'controller' => 'Telegram\MainBotController@handleRequests',
        ],
    ],

];
