<?php

namespace App\Services\Subscription;

use App\Models\App;

class BaseSubscriptionService
{
    public function handler(App $app)
    {
        // Get the platform config
        $platformConfig = config("subscription.AppPlatforms.{$app->platform->name}");

        // Get the handler class
        $handlerClass = $platformConfig['handler'];

        // Call the handler
        return resolve($handlerClass)->checkStatus($app);
    }
}
