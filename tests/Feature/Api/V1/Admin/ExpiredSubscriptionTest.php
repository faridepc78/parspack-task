<?php

namespace Api\V1\Admin;

use App\Enums\User\UserRoleEnum;
use App\Models\ExpiredSubscription;
use App\Models\User;
use Database\Seeders\AppSeeder;
use Database\Seeders\PlatformSeeder;
use Database\Seeders\SubscriptionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpiredSubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_not_see_count_of_last_expired_subscriptions_without_auth()
    {
        $this->makeDatabase();

        $email = 'user@gmail.com';
        $password = '12345678';

        $this->createUser($email, $password, UserRoleEnum::USER->value);

        $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode($email.':'.'1234'),
        ])->getJson(route('api.v1.admin.expired_subscriptions.index', [
            'type' => 'request',
        ]))->assertStatus(403);
    }

    public function test_user_can_not_see_count_of_last_expired_subscriptions_with_auth()
    {
        $this->makeDatabase();

        $email = 'user@gmail.com';
        $password = '12345678';

        $this->createUser($email, $password, UserRoleEnum::USER->value);

        $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode($email.':'.$password),
        ])->getJson(route('api.v1.admin.expired_subscriptions.index', [
            'type' => 'request',
        ]))->assertStatus(403);
    }

    public function test_admin_can_not_see_count_of_last_expired_subscriptions_without_auth()
    {
        $this->makeDatabase();

        $email = 'admin@gmail.com';
        $password = '12345678';

        $this->createUser($email, $password, UserRoleEnum::ADMIN->value);

        $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode($email.':'.'1234'),
        ])->getJson(route('api.v1.admin.expired_subscriptions.index', [
            'type' => 'request',
        ]))->assertStatus(403);
    }

    public function test_admin_can_not_see_count_of_last_expired_subscriptions_without_type()
    {
        $this->makeDatabase();

        $email = 'admin@gmail.com';
        $password = '12345678';

        $this->createUser($email, $password, UserRoleEnum::ADMIN->value);

        $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode($email.':'.$password),
        ])->getJson(route('api.v1.admin.expired_subscriptions.index'))->assertStatus(422);
    }

    public function test_admin_can_not_see_count_of_last_expired_subscriptions_with_wrong_type()
    {
        $this->makeDatabase();

        $email = 'admin@gmail.com';
        $password = '12345678';

        $this->createUser($email, $password, UserRoleEnum::ADMIN->value);

        $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode($email.':'.$password),
        ])->getJson(route('api.v1.admin.expired_subscriptions.index', [
            'type' => 'test',
        ]))->assertStatus(422);
    }

    public function test_admin_can_see_count_of_last_expired_subscriptions()
    {
        $this->makeDatabase();

        $email = 'admin@gmail.com';
        $password = '12345678';

        $this->createUser($email, $password, UserRoleEnum::ADMIN->value);

        $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode($email.':'.$password),
        ])->getJson(route('api.v1.admin.expired_subscriptions.index', [
            'type' => $this->faker->randomElement(ExpiredSubscription::types()),
        ]))->assertSuccessful()
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

    protected function createUser($email, $password, $role)
    {
        return User::factory()->create([
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);
    }
}
