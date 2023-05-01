<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Subscription model class
 *
 * @property int $id
 * @property int $app_id
 * @property string $status
 * @property Carbon|null $expires_at
 * @property Carbon|null $last_checked_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $guarded =
        [
            'id',
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'app_id',
            'status',
            'expires_at',
            'last_checked_at',
        ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    const ACTIVE_STATUS = 'active';
    const EXPIRED_STATUS = 'expired';
    const PENDING_STATUS = 'pending';

    public static array $statuses =
        [
            self::ACTIVE_STATUS,
            self::EXPIRED_STATUS,
            self::PENDING_STATUS,
        ];
}
