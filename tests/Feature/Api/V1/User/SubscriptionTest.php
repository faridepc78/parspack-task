<?php

namespace Api\V1\User;

use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Jobs\CheckSubscriptionJob;
use App\Models\App;
use App\Models\ExpiredSubscription;
use App\Models\Platform;
use App\Models\Subscription;
use Database\Seeders\PlatformSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_check_android_app_subscription_with_pending_status_and_passed_expires_at()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'android')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::response([
                'app_id' => $app->id,
                'app_name' => $app->name,
            ], 200),
        ]);

        $subscription =
            $this->createSubscription(
                $app->id,
                SubscriptionStatusEnum::PENDING->value,
                Carbon::now()->subDay()
            );

        $this->postJson(route('api.v1.subscriptions.check'), [
            'app_id' => $app->id,
        ])
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'message' => 'success request',
                'code' => 200,
                'data' => ['status' => 'active'],
            ]);

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    public function test_user_can_check_android_app_subscription_with_pending_status_and_not_passed_expires_at()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'android')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::response([
                'app_id' => $app->id,
                'app_name' => $app->name,
            ], 200),
        ]);

        $subscription =
            $this->createSubscription(
                $app->id,
                SubscriptionStatusEnum::PENDING->value,
                Carbon::now()->addDay()
            );

        $this->postJson(route('api.v1.subscriptions.check'), [
            'app_id' => $app->id,
        ])
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'message' => 'success request',
                'code' => 200,
                'data' => ['status' => 'active'],
            ]);

        $subscription->refresh();

        $this->assertEquals(SubscriptionStatusEnum::ACTIVE->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    public function test_user_can_check_android_app_subscription_with_pending_status_and_passed_expires_at_and_other_than_200_request()
    {
        Queue::fake([
            CheckSubscriptionJob::class,
        ]);

        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'android')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::response([
                'app_id' => $app->id,
                'app_name' => $app->name,
            ], 500),
        ]);

        $subscription =
            $this->createSubscription(
                $app->id,
                SubscriptionStatusEnum::PENDING->value,
                Carbon::now()->subDay()
            );


        $this->postJson(route('api.v1.subscriptions.check'), [
            'app_id' => $app->id,
        ])
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'message' => 'success request',
                'code' => 200,
                'data' => null,
            ]);

        Queue::assertPushed(CheckSubscriptionJob::class, function ($job) use ($app) {
            return $job->app->id === $app->id;
        });
    }

    protected function makeDatabase(): void
    {
        $this->seed([
            UserSeeder::class,
            PlatformSeeder::class,
        ]);
    }

    protected function createApp($platform_id)
    {
        return App::query()
            ->create([
                'name' => $this->faker->unique()->name(),
                'platform_id' => $platform_id,
            ]);
    }

    protected function createSubscription($app_id, $status, $expires_at)
    {
        return Subscription::query()
            ->create([
                'app_id' => $app_id,
                'status' => $status,
                'expires_at' => $expires_at,
            ]);
    }
}
