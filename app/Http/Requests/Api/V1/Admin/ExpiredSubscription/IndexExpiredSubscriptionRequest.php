<?php

namespace App\Http\Requests\Api\V1\Admin\ExpiredSubscription;

use App\Models\ExpiredSubscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexExpiredSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(ExpiredSubscription::types())],
        ];
    }
}
