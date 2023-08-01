<?php

namespace App\Enums\ExpiredSubscription;

enum ExpiredSubscriptionTypeEnum: string
{
    case REQUEST = 'request';
    case COMMAND = 'command';
}
