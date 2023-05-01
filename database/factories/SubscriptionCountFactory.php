<?php

namespace Database\Factories;

use App\Models\SubscriptionCount;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionCountFactory extends Factory
{
    protected $model = SubscriptionCount::class;

    public function definition(): array
    {
        return [
            'count' => $this->faker->numberBetween(1, 100),
            'checked_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
