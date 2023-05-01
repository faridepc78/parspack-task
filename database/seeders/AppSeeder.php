<?php

namespace Database\Seeders;

use App\Models\App;
use App\Repositories\App\AppRepositoryInterface;
use Illuminate\Database\Seeder;

class AppSeeder extends Seeder
{
    public function run()
    {
        $appRepository = resolve(AppRepositoryInterface::class);

        if (!$appRepository->getCount()) {
            App::factory(10)->create();
        } else {
            $this->command->warn('Apps has already been created');
        }
    }
}
