<?php

namespace App\Rules;

use App\Enums\Subscription\SubscriptionStatusEnum;
use App\Models\App;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckValidApp implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $app = App::query()->findOrFail($value);

        /* @phpstan-ignore-next-line */
        if ($app->subscription->status->value == SubscriptionStatusEnum::EXPIRED->value) {
            $fail('the app_id is invalid');
        }
    }
}
