<?php

namespace App\Services\Subscription;

use App\Jobs\CheckSubscriptionJob;
use App\Models\App;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class GooglePlaySubscriptionService
{
    use ApiResponser;

    public function checkStatus(App $app): JsonResponse
    {
        Http::fake();

        $response = Http::post($app->platform->http_service_url, [
            'app_id' => $app->id,
            'app_name' => $app->name,
        ]);

        if ($response->status() == 200) {

            $expiresAt = Carbon::parse($app->subscription->expires_at);

            if (Carbon::now()->gt($expiresAt)) {
                //
                dd('ok');
                // the "expires_at" datetime has passed
            } else {
                dd('gg');
                // the "expires_at" datetime is still in the future
            }
            dd($app->subscription->toArray());

            return $this->success_response(['status' => 'active'], 'success request');
        } else {
            CheckSubscriptionJob::dispatch($app)
                ->onConnection('database')
                ->delay(now()->addHour());

            return $this->success_response(null, 'success request');
        }
    }
}
