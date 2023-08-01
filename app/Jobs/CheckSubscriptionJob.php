<?php

namespace App\Jobs;

use App\Models\App;
use App\Services\Subscription\AppStoreSubscriptionService;
use App\Services\Subscription\GooglePlaySubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public App $app)
    {
    }

    public function handle()
    {
        switch ($this->app->platform->name) {
            case 'android':
                resolve(GooglePlaySubscriptionService::class)->checkStatus($this->app);
                break;
            case 'ios':
                resolve(AppStoreSubscriptionService::class)->checkStatus($this->app);
                break;
            default:
                return false;
        }
    }
}
