<?php

namespace App\Http\Requests\Api\V1\User\Subscription;

use App\Rules\CheckValidApp;
use Illuminate\Foundation\Http\FormRequest;

class CheckSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app_id' => ['required', new CheckValidApp()],
        ];
    }
}
