<?php

namespace Api\V1\User;

use App\Models\App;
use Database\Seeders\AppSeeder;
use Database\Seeders\PlatformSeeder;
use Database\Seeders\SubscriptionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_not_see_app_details_with_not_available_id()
    {
        $this->makeDatabase();

        $this->getJson(route('api.v1.apps.show', 500))
            ->assertStatus(404);
    }

    public function test_user_can_see_app_details_with_available_id()
    {
        $this->makeDatabase();

        $app = App::query()->first();

        $this->getJson(route('api.v1.apps.show', $app->id))
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
                'data',
            ]);
    }

    protected function makeDatabase(): void
    {
        $this->seed([
            UserSeeder::class,
            PlatformSeeder::class,
            AppSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }
}
