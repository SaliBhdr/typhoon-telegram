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
        'mainBot' => [
            'is_active' => true,
            'baseUrl' => 'https://5df7d54d.ngrok.io',
            'botToken' => '554656542:AAGTEmWvM3oMaZq5ejsBf7Ag8i7oKNgdv2Q',
            'controller' => 'Telegram\V1\MainBotController@handleRequests'
        ],
        'poster-bot-1' => [
            'is_active' => false,
            'baseUrl' => 'https://a2db17a0.ngrok.io',
            'botToken' => '474282837:AAHZn7vaGmz6R938MrHUnq36aU8uO8IzEj8',
            'controller' => 'Telegram\V1\PosterBotController@handleRequests'
        ],
        'poster-bot-2' => [
            'is_active' => false,
            'baseUrl' => 'https://a2db17a0.ngrok.io',
            'botToken' => '474936749:AAFZ_d_cYEoaWzkDMyO_nks59DrqmVjgLHw',
            'controller' => 'Telegram\V1\PosterBotController@handleRequests'
        ],
    ]

];
