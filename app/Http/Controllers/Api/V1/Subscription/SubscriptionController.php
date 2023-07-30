<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Services\Subscription\AppStoreSubscriptionService;
use App\Services\Subscription\GooglePlaySubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(protected GooglePlaySubscriptionService $googlePlaySubscriptionService,
                                protected AppStoreSubscriptionService   $appStoreSubscriptionService)
    {

    }

    public function check(Request $request)
    {
        $app = App::query()->find(1);

        return $this->googlePlaySubscriptionService->checkStatus($app);
    }
}
