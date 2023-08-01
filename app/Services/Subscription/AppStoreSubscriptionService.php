<?php

namespace App\Services\Subscription;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class AppStoreSubscriptionService extends DefaultSubscriptionService implements SubscriptionServiceInterface
{
    public static function getDelayRequestForLater(): Carbon
    {
        return now()->addHours(2);
    }

    public static function getSuccessResponse(): JsonResponse
    {
        return self::success_response(
            ['subscription' => 'expired'],
            'success request'
        );
    }
}
