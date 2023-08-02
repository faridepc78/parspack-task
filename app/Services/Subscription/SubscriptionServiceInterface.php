<?php

namespace App\Services\Subscription;

use App\Models\App;

interface SubscriptionServiceInterface
{
    public static function checkStatus(
        App $app,
        string $expired_subscriptions_token,
        bool $command
    );
}
