<?php

namespace Api\V1\User;

use App\Enums\Log\LogTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Enums\User\UserRoleEnum;
use App\Jobs\CheckSubscriptionJob;
use App\Jobs\SendExpiredSubscriptionJob;
use App\Models\App;
use App\Models\ExpiredSubscription;
use App\Models\Log;
use App\Models\Platform;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SendExpiredSubscriptionNotification;
use Database\Seeders\PlatformSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /* start user check android subscription with expired status*/
    public function test_user_can_not_check_android_app_subscription_with_expired_status()
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

        $this->createSubscription(
            $app->id,
            SubscriptionStatusEnum::EXPIRED->value,
            Carbon::now()->subDay()
        );

        $this->postJson(route('api.v1.subscriptions.check'), [
            'app_id' => $app->id,
        ])->assertStatus(422);
    }

    /* end user check android subscription with expired status*/

    /* start user check android subscription with pending status*/

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
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'android')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
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
                'data' => null,
            ]);

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    public function test_user_can_check_android_app_subscription_with_pending_status_and_not_passed_expires_at_and_other_than_200_request()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'android')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
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
                'data' => null,
            ]);

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(SubscriptionStatusEnum::ACTIVE->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    /* end user check android subscription with pending status*/

    /* start user check android subscription with active status*/

    public function test_user_can_check_android_app_subscription_with_active_status_and_passed_expires_at()
    {
        $this->makeDatabase();

        Notification::fake();

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
                SubscriptionStatusEnum::ACTIVE->value,
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

        $this->assertDatabaseCount('jobs', 1);
        $this->assertDatabaseCount('logs', 1);

        $admin = User::query()
            ->where('role', '=', UserRoleEnum::ADMIN->value)
            ->firstOrFail();

        $log = Log::query()
            ->create(self::prepareLogDataForSendMail($admin, $app));

        $job = new SendExpiredSubscriptionJob($admin, $app, $log);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);

        $this->assertNotNull($log->sent_at);
        $this->assertNotNull($log->is_sent);

        Notification::assertSentTo(
            $admin,
            SendExpiredSubscriptionNotification::class,
            function ($notification, $channels) use ($admin) {
                $this->assertContains('mail', $channels);

                return $notification->toMail($admin)->subject === 'expired subscription notification';
            }
        );
    }

    public function test_user_can_check_android_app_subscription_with_active_status_and_not_passed_expires_at()
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
                SubscriptionStatusEnum::ACTIVE->value,
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

    public function test_user_can_check_android_app_subscription_with_active_status_and_passed_expires_at_and_other_than_200_request()
    {
        $this->makeDatabase();

        Notification::fake();

        $platform = Platform::query()
            ->where('name', '=', 'android')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 200),
        ]);

        $subscription =
            $this->createSubscription(
                $app->id,
                SubscriptionStatusEnum::ACTIVE->value,
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

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $this->assertDatabaseCount('jobs', 2);
        $this->assertDatabaseCount('logs', 1);

        $admin = User::query()
            ->where('role', '=', UserRoleEnum::ADMIN->value)
            ->firstOrFail();

        $log = Log::query()
            ->create(self::prepareLogDataForSendMail($admin, $app));

        $job = new SendExpiredSubscriptionJob($admin, $app, $log);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);

        $this->assertNotNull($log->sent_at);
        $this->assertNotNull($log->is_sent);

        Notification::assertSentTo(
            $admin,
            SendExpiredSubscriptionNotification::class,
            function ($notification, $channels) use ($admin) {
                $this->assertContains('mail', $channels);

                return $notification->toMail($admin)->subject === 'expired subscription notification';
            }
        );
    }

    public function test_user_can_check_android_app_subscription_with_active_status_and_not_passed_expires_at_and_other_than_200_request()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'android')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 200),
        ]);

        $subscription =
            $this->createSubscription(
                $app->id,
                SubscriptionStatusEnum::ACTIVE->value,
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
                'data' => null,
            ]);

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(SubscriptionStatusEnum::ACTIVE->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    /* end user check android subscription with active status*/

    /* start user check ios subscription with expired status*/

    public function test_user_can_not_check_ios_app_subscription_with_expired_status()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::response([
                'app_id' => $app->id,
                'app_name' => $app->name,
            ], 200),
        ]);

        $this->createSubscription(
            $app->id,
            SubscriptionStatusEnum::EXPIRED->value,
            Carbon::now()->subDay()
        );

        $this->postJson(route('api.v1.subscriptions.check'), [
            'app_id' => $app->id,
        ])->assertStatus(422);
    }

    /* end user check ios subscription with expired status*/

    /* start user check ios subscription with pending status*/

    public function test_user_can_check_ios_app_subscription_with_pending_status_and_passed_expires_at()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
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
                'data' => ['subscription' => 'expired'],
            ]);

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    public function test_user_can_check_ios_app_subscription_with_pending_status_and_not_passed_expires_at()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
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
                'data' => ['subscription' => 'expired'],
            ]);

        $subscription->refresh();

        $this->assertEquals(SubscriptionStatusEnum::ACTIVE->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    public function test_user_can_check_ios_app_subscription_with_pending_status_and_passed_expires_at_and_other_than_200_request()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
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
                'data' => null,
            ]);

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    public function test_user_can_check_ios_app_subscription_with_pending_status_and_not_passed_expires_at_and_other_than_200_request()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
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
                'data' => null,
            ]);

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(SubscriptionStatusEnum::ACTIVE->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    /* end user check ios subscription with pending status*/

    /* start user check ios subscription with active status*/

    public function test_user_can_check_ios_app_subscription_with_active_status_and_passed_expires_at()
    {
        $this->makeDatabase();

        Notification::fake();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
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
                SubscriptionStatusEnum::ACTIVE->value,
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
                'data' => ['subscription' => 'expired'],
            ]);

        $this->assertDatabaseCount('jobs', 1);
        $this->assertDatabaseCount('logs', 1);

        $admin = User::query()
            ->where('role', '=', UserRoleEnum::ADMIN->value)
            ->firstOrFail();

        $log = Log::query()
            ->create(self::prepareLogDataForSendMail($admin, $app));

        $job = new SendExpiredSubscriptionJob($admin, $app, $log);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);

        $this->assertNotNull($log->sent_at);
        $this->assertNotNull($log->is_sent);

        Notification::assertSentTo(
            $admin,
            SendExpiredSubscriptionNotification::class,
            function ($notification, $channels) use ($admin) {
                $this->assertContains('mail', $channels);

                return $notification->toMail($admin)->subject === 'expired subscription notification';
            }
        );
    }

    public function test_user_can_check_ios_app_subscription_with_active_status_and_not_passed_expires_at()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
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
                SubscriptionStatusEnum::ACTIVE->value,
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
                'data' => ['subscription' => 'expired'],
            ]);

        $subscription->refresh();

        $this->assertEquals(SubscriptionStatusEnum::ACTIVE->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    public function test_user_can_check_ios_app_subscription_with_active_status_and_passed_expires_at_and_other_than_200_request()
    {
        $this->makeDatabase();

        Notification::fake();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 200),
        ]);

        $subscription =
            $this->createSubscription(
                $app->id,
                SubscriptionStatusEnum::ACTIVE->value,
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

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $this->assertDatabaseCount('jobs', 2);
        $this->assertDatabaseCount('logs', 1);

        $admin = User::query()
            ->where('role', '=', UserRoleEnum::ADMIN->value)
            ->firstOrFail();

        $log = Log::query()
            ->create(self::prepareLogDataForSendMail($admin, $app));

        $job = new SendExpiredSubscriptionJob($admin, $app, $log);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(1, ExpiredSubscription::query()->count());
        $this->assertEquals(SubscriptionStatusEnum::EXPIRED->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);

        $this->assertNotNull($log->sent_at);
        $this->assertNotNull($log->is_sent);

        Notification::assertSentTo(
            $admin,
            SendExpiredSubscriptionNotification::class,
            function ($notification, $channels) use ($admin) {
                $this->assertContains('mail', $channels);

                return $notification->toMail($admin)->subject === 'expired subscription notification';
            }
        );
    }

    public function test_user_can_check_ios_app_subscription_with_active_status_and_not_passed_expires_at_and_other_than_200_request()
    {
        $this->makeDatabase();

        $platform = Platform::query()
            ->where('name', '=', 'ios')
            ->first();

        $app = $this->createApp($platform->id);

        Http::fake([
            $app->platform->http_service_url => Http::sequence()
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 500)
                ->push([
                    'app_id' => $app->id,
                    'app_name' => $app->name,
                ], 200),
        ]);

        $subscription =
            $this->createSubscription(
                $app->id,
                SubscriptionStatusEnum::ACTIVE->value,
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
                'data' => null,
            ]);

        $this->assertDatabaseCount('jobs', 1);

        $job = new CheckSubscriptionJob($app);
        $job->handle();

        $subscription->refresh();

        $this->assertEquals(SubscriptionStatusEnum::ACTIVE->value, $subscription->status->value);
        $this->assertNotNull($subscription->checked_at);
    }

    /* end user check ios subscription with active status*/

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

    protected function prepareLogDataForSendMail(User $user, App $app): array
    {
        return [
            'recipient' => $user->email,
            'subject' => 'expired subscription notification',
            'type' => LogTypeEnum::EMAIL->value,
            'notification' => SendExpiredSubscriptionNotification::class,
            'details' => [
                'platform' => $app->platform->name,
                'app_id' => $app->id,
                'admin_id' => $user->id,
            ],
            'saved_at' => Carbon::now(),
        ];
    }
}
