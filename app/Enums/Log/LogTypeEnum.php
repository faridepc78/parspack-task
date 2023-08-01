<?php

namespace App\Enums\Log;

enum LogTypeEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
}
