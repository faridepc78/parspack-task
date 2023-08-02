<?php

namespace App\Services\Subscription;

use App\Models\App;

class BaseSubscriptionService
{
    public static function handler(
        App $app,
        string $expired_subscriptions_token,
        bool $command
    ) {
        // Get the platform config
        $platformConfig = config("subscription.AppPlatforms.{$app->platform->name}");

        // Get the handler class
        $handlerClass = $platformConfig['handler'];

        // Call the handler
        return resolve($handlerClass)::checkStatus($app, $expired_subscriptions_token, $command);
    }
}
