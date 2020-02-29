<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 2/3/2020
 * Time: 12:10 PM
 */


if (!function_exists('telegram')) {

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return \SaliBhdr\TyphoonTelegram\Telegram\Api
     */
    function telegram()
    {
        return app()->make('telegram');
    }
}


if (!function_exists('fixCommandName')) {

    /**
     * @param string $commandName
     * @return string
     */
    function fixCommandName(string $commandName)
    {
        if (strpos($commandName, "/") !== 0)
            $commandName = '/' . $commandName;

        return $commandName;
    }
}