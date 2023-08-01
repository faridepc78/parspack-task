<?php

use App\Services\Subscription\AppStoreSubscriptionService;
use App\Services\Subscription\GooglePlaySubscriptionService;

return [
    'AppPlatforms' => [
        'android' => [
            'handler' => GooglePlaySubscriptionService::class,
        ],
        'ios' => [
            'handler' => AppStoreSubscriptionService::class,
        ],
    ],
];
