<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * User model class
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $guarded =
        [
            'id',
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'name',
            'email',
            'password',
            'role',
        ];

    protected $hidden =
        [
            'password',
        ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    const ADMIN_ROLE = 'admin';

    const USER_ROLE = 'user';

    public static array $roles =
        [
            self::ADMIN_ROLE,
            self::USER_ROLE,
        ];

    public static array $admin = [
        'name' => 'admin',
        'email' => 'admin@gmail.com',
        'password' => 12345678,
        'role' => User::ADMIN_ROLE,
    ];
}
