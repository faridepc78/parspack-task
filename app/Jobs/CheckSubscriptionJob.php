<?php

namespace App\Jobs;

use App\Models\App;
use App\Services\Subscription\BaseSubscriptionService;
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
        $token = make_token(10);

        return resolve(BaseSubscriptionService::class)::handler($this->app, $token, false);
    }
}
