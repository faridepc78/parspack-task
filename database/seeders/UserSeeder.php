<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::query()->count()) {
            User::query()->create(User::$admin);

            User::factory(10)->create();
        } else {
            $this->command->warn('Users has already been created');
        }
    }
}
