<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Repositories\Subscription\SubscriptionRepositoryInterface;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptionRepository = resolve(SubscriptionRepositoryInterface::class);

        if (!$subscriptionRepository->getCount()) {
            Subscription::factory(10)->create();
        } else {
            $this->command->warn('Subscriptions has already been created');
        }
    }
}
