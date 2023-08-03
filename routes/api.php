<?php

use App\Enums\User\UserRoleEnum;
use App\Http\Controllers\Api\V1\Admin\ExpiredSubscriptionController;
use App\Http\Controllers\Api\V1\User\AppController;
use App\Http\Controllers\Api\V1\User\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->as('api.v1.')
    ->middleware(['api', 'throttle:50,1'])
    ->group(function () {
        Route::post(
            'subscriptions/check',
            [SubscriptionController::class, 'check']
        )
            ->name('subscriptions.check');

        Route::apiResource('apps', AppController::class)
            ->only('show');

        Route::prefix('admin')
            ->name('admin.')
            ->middleware('check_role:'.UserRoleEnum::ADMIN->value)
            ->group(function () {
                Route::apiResource('expired_subscriptions', ExpiredSubscriptionController::class)
                    ->only('index');
            });
    });
