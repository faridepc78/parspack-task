<?php

namespace App\Services\Subscription;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class GooglePlaySubscriptionService extends DefaultSubscriptionService implements SubscriptionServiceInterface
{
    public static function getDelayRequestForLater(): Carbon
    {
        return now()->addHour();
    }

    public static function getSuccessResponse(): JsonResponse
    {
        return self::success_response(
            ['status' => 'active'],
            'success request'
        );
    }
}
