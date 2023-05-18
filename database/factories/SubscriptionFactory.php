<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Repositories\App\AppRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $appRepository = resolve(AppRepositoryInterface::class);

        return [
            'app_id' => $appRepository->getRandom()->id,
            'status' => $this->faker->randomElement(Subscription::statuses()),
            'expires_at' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'last_checked_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
