<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Repositories\Platform\PlatformRepositoryInterface;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platformRepository = resolve(PlatformRepositoryInterface::class);

        if (!$platformRepository->getCount()) {
            foreach (Platform::$defaultPlatforms as $platform) {
                $platformRepository->create($platform);
            }
        } else {
            $this->command->warn('Platforms has already been created');
        }
    }
}
