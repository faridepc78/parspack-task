<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * SubscriptionCount model class
 *
 * @property int $id
 * @property int $count
 * @property Carbon $checked_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SubscriptionCount extends Model
{
    use HasFactory;

    protected $table = 'subscription_counts';

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
