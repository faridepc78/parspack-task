<?php

namespace App\Services\Subscription;

use App\Jobs\CheckSubscriptionJob;
use App\Models\App;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AppStoreSubscriptionService
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
            return $this->success_response(['subscription' => 'expired'], 'success request');
        } else {
            CheckSubscriptionJob::dispatch($app)
                ->onConnection('database')
                ->delay(now()->addHours(2));

            return $this->success_response(null, 'success request');
        }
    }
}
