<?php

namespace Database\Seeders;

use App\Models\App;
use Illuminate\Database\Seeder;

class AppSeeder extends Seeder
{
    public function run(): void
    {
        if (! App::query()->count()) {
            App::factory(100)->create();
        } else {
            $this->command->warn('Apps has already been created');
        }
    }
}
