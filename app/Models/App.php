<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App model class
 *
 * @property int $id
 * @property string $name
 * @property int platform_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class App extends Model
{
    use HasFactory;

    protected $table = 'apps';

    protected $guarded =
        [
            'id',
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'name',
            'platform_id',
        ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
