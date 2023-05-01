<?php

namespace Database\Factories;

use App\Models\App;
use App\Repositories\Platform\PlatformRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppFactory extends Factory
{
    protected $model = App::class;

    public function definition(): array
    {
        $platformRepository = resolve(PlatformRepositoryInterface::class);

        return [
            'name' => $this->faker->name,
            'platform_id' => $platformRepository->getRandomByNames(['android', 'ios'])
        ];
    }
}
