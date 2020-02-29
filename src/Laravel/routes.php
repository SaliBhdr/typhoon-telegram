<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 1/24/2020
 * Time: 9:20 PM
 */

if (app('config')->get('telegram.automatic-routes',false)) {

    $bots = app('config')->get('telegram.bots',[]);

    if (!empty($bots)) {
        foreach ($bots as $bot) {
            if ($bot['is_active'] ?? false) {
                Route::post("{$bot['botToken']}/webhook", "{$bot['controller']}");
            }
        }
    }
}

