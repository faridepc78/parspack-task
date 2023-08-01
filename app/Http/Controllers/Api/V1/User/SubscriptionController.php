<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Subscription\CheckSubscriptionRequest;
use App\Models\App;
use App\Services\Subscription\BaseSubscriptionService;

class SubscriptionController extends Controller
{
    public function __construct(protected BaseSubscriptionService $baseSubscriptionService)
    {
    }

    public function check(CheckSubscriptionRequest $request)
    {
        $app = App::query()->find($request->input('app_id'));

        return $this->baseSubscriptionService::handler($app);
    }
}
