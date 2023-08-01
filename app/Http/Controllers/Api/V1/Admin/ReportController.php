<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\ExpiredSubscription\ExpiredSubscriptionResource;
use App\Models\ExpiredSubscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::in(ExpiredSubscription::types())],
        ]);

        $type = $request->input('type');

        $data = ExpiredSubscription::query()
            ->where('type', '=', $type)
            ->latest()
            ->first();

        return self::success_response(
            ExpiredSubscriptionResource::make($data),
            'show last record expired subscription'
        );
    }
}
