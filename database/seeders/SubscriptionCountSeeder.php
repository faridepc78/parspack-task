<?php

namespace Database\Seeders;

use App\Models\SubscriptionCount;
use App\Repositories\SubscriptionCount\SubscriptionCountRepositoryInterface;
use Illuminate\Database\Seeder;

class SubscriptionCountSeeder extends Seeder
{
    public function run()
    {
        $subscriptionCountRepository = resolve(SubscriptionCountRepositoryInterface::class);

        if (!$subscriptionCountRepository->getCount()) {
            SubscriptionCount::factory(10)->create();
        } else {
            $this->command->warn('SubscriptionCounts has already been created');
        }
    }
}
