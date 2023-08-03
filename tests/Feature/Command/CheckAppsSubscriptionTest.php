<?php

namespace Command;

use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\App;
use App\Models\Platform;
use App\Models\Subscription;
use Database\Seeders\PlatformSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckAppsSubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_run_command_successful()
    {
        $this->makeDatabase();

        for ($i = 0; $i < 50; $i++) {
            App::query()
                ->create([
                    'name' => $this->faker->unique()->name(),
                    'platform_id' => Platform::query()
                        ->inRandomOrder()
                        ->first()
                        ->id,
                ]);
        }

        $active_apps = App::query()
            ->orderBy('id', 'asc')
            ->limit(25)
            ->get();

        $expired_apps = App::query()
            ->orderBy('id', 'desc')
            ->limit(25)
            ->get();

        foreach ($active_apps as $item) {
            Subscription::query()
                ->create([
                    'app_id' => $item->id,
                    'status' => SubscriptionStatusEnum::PENDING->value,
                    'expires_at' => $this->faker->dateTimeBetween('+1 day', '+7 day'),
                ]);
        }

        foreach ($expired_apps as $item) {
            Subscription::query()
                ->create([
                    'app_id' => $item->id,
                    'status' => SubscriptionStatusEnum::PENDING->value,
                    'expires_at' => $this->faker->dateTimeBetween('-7 day', '-1 day'),
                ]);
        }

        $this->artisan('apps:check_subscription')
            ->assertSuccessful();

        $pending_apps_count = App::query()
            ->whereHas('subscription', function (Builder $query) {
                $query->where('status', '=', SubscriptionStatusEnum::PENDING->value);
            })
            ->count();

        $active_apps_count = App::query()
            ->whereHas('subscription', function (Builder $query) {
                $query->where('status', '=', SubscriptionStatusEnum::ACTIVE->value);
            })
            ->count();

        $expired_apps_count = App::query()
            ->whereHas('subscription', function (Builder $query) {
                $query->where('status', '=', SubscriptionStatusEnum::EXPIRED->value);
            })
            ->count();

        $this->assertEquals(0, $pending_apps_count);
        $this->assertEquals(25, $active_apps_count);
        $this->assertEquals(25, $expired_apps_count);
    }

    protected function makeDatabase(): void
    {
        $this->seed([
            UserSeeder::class,
            PlatformSeeder::class,
        ]);
    }
}
