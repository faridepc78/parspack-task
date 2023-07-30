<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Platform model class
 *
 * @property int $id
 * @property string $name
 * @property string $http_service_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Platform extends Model
{
    use HasFactory;

    protected $table = 'platforms';

    protected $guarded =
        [
            'id',
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'name',
            'http_service_url',
        ];

    public static array $defaultPlatforms = [
        [
            'name' => 'android',
            'http_service_url' => 'https://api.play.google.com/subscription',
        ],
        [
            'name' => 'ios',
            'http_service_url' => 'https://api.appstore.com/subscription',
        ],
    ];
}
