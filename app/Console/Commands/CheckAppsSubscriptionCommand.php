<?php

namespace App\Console\Commands;

use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\App;
use App\Services\Subscription\BaseSubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CheckAppsSubscriptionCommand extends Command
{
    protected $signature = 'apps:check_subscription';

    protected $description = 'Check app subscription every weekend';

    public function handle(): void
    {
        $apps = App::query()
            ->whereHas('subscription', function (Builder $query) {
                $query->where('status', '!=', SubscriptionStatusEnum::EXPIRED->value);
            })
            ->get();

        $token = make_token(10);

        $baseSubscriptionService = resolve(BaseSubscriptionService::class);

        foreach ($apps as $app) {
            $baseSubscriptionService::handler($app, $token, true);
        }

        $this->info('mission accomplished');
    }
}
