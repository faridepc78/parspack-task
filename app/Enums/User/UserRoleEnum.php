<?php

namespace App\Enums\User;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
}
