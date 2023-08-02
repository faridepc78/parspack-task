<?php

namespace Database\Factories;

use App\Models\App;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'app_id' => $this->faker->unique()->randomElement(App::all()->pluck('id')->toArray()),
            'status' => $this->faker->randomElement(['pending', 'active']),
            'expires_at' => $this->faker->dateTimeBetween('-7 day', '-1 day'),
        ];
    }
}
