<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        if (! Subscription::query()->count()) {
            Subscription::factory(50)->create();
        } else {
            $this->command->warn('Subscriptions has already been created');
        }
    }
}
