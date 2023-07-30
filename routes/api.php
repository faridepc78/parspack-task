<?php

use App\Http\Controllers\Api\V1\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->as('api.v1.')
    ->middleware(['api', 'throttle:50,1'])
    ->group(function () {

        // #region Subscription
        Route::post(
            'subscriptions/check',
            [SubscriptionController::class, 'check']
        )
            ->name('subscriptions.check');
        // #endregion
    });
