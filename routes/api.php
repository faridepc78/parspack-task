<?php

use App\Enums\User\UserRoleEnum;
use App\Http\Controllers\Api\V1\Admin\ReportController;
use App\Http\Controllers\Api\V1\TestController;
use App\Http\Controllers\Api\V1\User\AppController;
use App\Http\Controllers\Api\V1\User\SubscriptionController;
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

        Route::apiResource('apps', AppController::class)
            ->only('show');

        Route::get('admin/reports/expired_subscription', ReportController::class)
            ->name('admin.reports.expired-subscription')
            ->middleware('check_role:'.UserRoleEnum::ADMIN->value);

        Route::post('test', TestController::class)
            ->name('test');
    });
