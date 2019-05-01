<?php
/**
 * User: Salar Bahador
 * Date: 4/27/2019
 * Time: 1:37 AM
 */

$webhooks = $config['webhooks'] ?? null;

if (!is_null($webhooks) && !empty($webhooks)) {

    foreach ($webhooks as $webhook) {

        if ($webhook['is_active']) {
            app()->router->post(
                "/{$webhook['botToken']}/webhook",
                "App\Http\Controllers\\"."{$webhook['controller']}");
        }
    }
}
