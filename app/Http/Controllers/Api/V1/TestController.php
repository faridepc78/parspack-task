<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\User\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\User;
use App\Notifications\SendExpiredSubscriptionNotification;

class TestController extends Controller
{
    public function __invoke()
    {
        $admin = User::query()
            ->where('role', '=', UserRoleEnum::ADMIN)
            ->first();

        $app = App::query()->first();

        $admin->
        notify(new SendExpiredSubscriptionNotification($app));

        dd('ok');
    }
}
