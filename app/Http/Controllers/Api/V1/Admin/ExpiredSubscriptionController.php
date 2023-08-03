<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ExpiredSubscription\IndexExpiredSubscriptionRequest;
use App\Http\Resources\Api\V1\Admin\ExpiredSubscription\ExpiredSubscriptionResource;
use App\Models\ExpiredSubscription;

class ExpiredSubscriptionController extends Controller
{
    public function index(IndexExpiredSubscriptionRequest $request)
    {
        $type = $request->input('type');

        $expiredSubscription = ExpiredSubscription::query()
            ->where('type', '=', $type)
            ->latest()
            ->first();

        if ($expiredSubscription !== null) {
            return self::success_response(
                ExpiredSubscriptionResource::make($expiredSubscription),
                'show last record expired subscription'
            );
        } else {
            return self::success_response(
                null,
                'no expired subscriptions found'
            );
        }
    }
}
