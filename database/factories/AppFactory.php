<?php

namespace Database\Factories;

use App\Models\App;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppFactory extends Factory
{
    protected $model = App::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'platform_id' => Platform::query()
                ->inRandomOrder()
                ->first()
                ->id,
        ];
    }
}
