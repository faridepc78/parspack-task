<?php

namespace Database\Seeders;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $userRepository = resolve(UserRepositoryInterface::class);

        if (!$userRepository->getCount()) {
            $userRepository->create(User::$admin);

            User::factory(10)->create();
        } else {
            $this->command->warn('Users has already been created');
        }
    }
}
