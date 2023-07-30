<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * ExpiredSubscription model class
 *
 * @property int $id
 * @property int $count
 * @property Carbon $checked_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ExpiredSubscription extends Model
{
    protected $table = 'expired_subscriptions';

    protected $guarded =
        [
            'id',
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'count',
            'checked_at',
        ];
}
