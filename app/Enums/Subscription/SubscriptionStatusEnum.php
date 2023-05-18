<?php

namespace App\Enums\Subscription;

enum SubscriptionStatusEnum: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case PENDING = 'pending';
}
