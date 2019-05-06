<?php

return [
    'automatic-routes' => true,
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
    'default_bot_token' => env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN'),

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
    | Should be an instance of \Telegram\Bot\HttpClients\HttpClientInterface
    |
    | Default: GuzzlePHP
    |
    */

    'http_client_handler' => null,

    'guzzle' => [
        'ssl-certificate' => false,
        'http-proxy' => [
            'use-proxy' => false,
            'ip' => 'proxy-ip',
            'port' => 'port',
            'username' => 'username',
            'password' => 'password'
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Register Telegram Commands [Optional]
    |--------------------------------------------------------------------------
    |
    | If you'd like to use the SDK's built in command handler system,
    | You can register all the commands here.
    |
    | The command class should extend the \Telegram\Bot\Commands\Command class.
    |
    | Default: The SDK registers, a help command which when a user sends /help
    | will respond with a list of available commands and description.
    |
    */
    'commands' => [
        Telegram\Bot\Commands\HelpCommand::class,
    ],


    'bots' => [
        'CustomBotName' => [
            'is_active' => false,
            'baseUrl' => 'HOST DOMAIN',
            'botToken' => 'YOUR BOT TOKEN',
            'controller' => 'Telegram\V1\MainBotController@handleRequests'
        ],
        'CustomBotName2' => [
            'is_active' => false,
            'baseUrl' => 'HOST DOMAIN',
            'botToken' => 'YOUR BOT TOKEN',
            'controller' => 'Telegram\V1\SecondBotController@handleRequests'
        ],
    ]

];
