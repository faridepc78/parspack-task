<?php

namespace App\Providers;

use App\Repositories\App\AppRepository;
use App\Repositories\App\AppRepositoryInterface;
use App\Repositories\Platform\PlatformRepository;
use App\Repositories\Platform\PlatformRepositoryInterface;
use App\Repositories\Subscription\SubscriptionRepository;
use App\Repositories\Subscription\SubscriptionRepositoryInterface;
use App\Repositories\SubscriptionCount\SubscriptionCountRepository;
use App\Repositories\SubscriptionCount\SubscriptionCountRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            PlatformRepositoryInterface::class,
            PlatformRepository::class
        );

        $this->app->bind(
            AppRepositoryInterface::class,
            AppRepository::class
        );

        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            SubscriptionRepository::class
        );

        $this->app->bind(
            SubscriptionCountRepositoryInterface::class,
            SubscriptionCountRepository::class
        );
    }
}
