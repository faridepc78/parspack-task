<?php

namespace App\Services\Subscription;

use App\Enums\Log\LogTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Enums\User\UserRoleEnum;
use App\Jobs\CheckSubscriptionJob;
use App\Jobs\SendExpiredSubscriptionJob;
use App\Models\App;
use App\Models\Log;
use App\Models\User;
use App\Notifications\SendExpiredSubscriptionNotification;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

abstract class DefaultSubscriptionService
{
    use ApiResponser;

    private static Carbon $delay;

    private static JsonResponse $successResponse;

    private function __construct()
    {
        self::$delay = self::getDelayRequestForLater(); // @phpstan-ignore-line
        self::$successResponse = self::getSuccessResponse(); // @phpstan-ignore-line
    }

    abstract public static function getDelayRequestForLater();

    abstract public static function getSuccessResponse();

    public static function checkStatus(App $app): JsonResponse
    {
        $response = self::sendRequest($app);

        if ($response->ok()) {
            $expiresAt = Carbon::parse($app->subscription->expires_at);
            $subscriptionStatus = $app->subscription->status;

            if (Carbon::now()->gt($expiresAt)) {
                $newSubscriptionStatus = SubscriptionStatusEnum::EXPIRED->value;
                if ($subscriptionStatus === SubscriptionStatusEnum::ACTIVE->value) {
                    self::sendExpiredSubscriptionNotification($app);
                }
            } else {
                $newSubscriptionStatus = SubscriptionStatusEnum::ACTIVE->value;
            }

            self::updateSubscription($app, $newSubscriptionStatus);

            return self::$successResponse;
        } else {
            return self::runRequestForLater($app);
        }
    }

    private static function runRequestForLater(App $app): JsonResponse
    {
        CheckSubscriptionJob::dispatch($app)
            ->onConnection('database')
            ->delay(self::$delay);

        return self::success_response(
            null,
            'success request'
        );
    }

    private static function sendRequest(App $app): Response
    {
        Http::fake();

        return Http::post($app->platform->http_service_url, [
            'app_id' => $app->id,
            'app_name' => $app->name,
        ]);
    }

    private static function updateSubscription(App $app, $status = null)
    {
        $values = ['checked_at' => Carbon::now()];

        if ($status !== null) {
            $values['status'] = $status;
        }

        return $app->subscription->update($values);
    }

    private static function sendExpiredSubscriptionNotification(App $app): PendingDispatch
    {
        $admin = User::query()
            ->where('role', '=', UserRoleEnum::ADMIN->value)
            ->firstOrFail();

        $log = Log::query()
            ->create(self::prepareLogDataForSendMail($admin, $app));

        return SendExpiredSubscriptionJob::dispatch($admin, $app, $log);
    }

    private static function prepareLogDataForSendMail(User $user, App $app): array
    {
        return [
            'recipient' => $user->email,
            'subject' => 'expired subscription notification',
            'type' => LogTypeEnum::EMAIL->value,
            'notification' => SendExpiredSubscriptionNotification::class,
            'details' => [
                'platform' => $app->platform->name,
                'app_id' => $app->id,
                'admin_id' => $user->id,
            ],
            'saved_at' => Carbon::now(),
        ];
    }
}
