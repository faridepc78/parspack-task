<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        if (! Platform::query()->count()) {
            foreach (Platform::$defaultPlatforms as $platform) {
                Platform::query()->create($platform);
            }
        } else {
            $this->command->warn('Platforms has already been created');
        }
    }
}
