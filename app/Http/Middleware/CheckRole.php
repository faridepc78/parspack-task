<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CheckRole
{
    use ApiResponser;

    public function handle(Request $request, Closure $next, $role)
    {
        $email = $request->header('php-auth-user');
        $password = $request->header('php-auth-pw');

        if (! $email || ! $password) {
            return self::error_response(
                403,
                'You do not have access to this section'
            );
        }

        $admin = User::query()
            ->where([
                ['email', '=', $email],
                ['role', '=', $role],
            ])->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            return self::error_response(
                403,
                'You do not have access to this section'
            );
        }

        return $next($request);
    }
}
