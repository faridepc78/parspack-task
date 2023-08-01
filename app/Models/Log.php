<?php

namespace App\Models;

use App\Enums\Log\LogTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Log model class
 *
 * @property int $id
 * @property string $recipient
 * @property string|null $subject
 * @property string|null $body
 * @property string $type
 * @property string $notification
 * @property array|null $details
 * @property Carbon $saved_at
 * @property Carbon|null $sent_at
 * @property string|null $error_message
 * @property bool|null $is_sent
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Log extends Model
{
    protected $table = 'logs';

    protected $guarded =
        [
            'id',
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'recipient',
            'subject',
            'body',
            'type',
            'notification',
            'details',
            'saved_at',
            'sent_at',
            'error_message',
            'is_sent',
        ];

    protected $casts =
        [
            'details' => 'array',
            'is_sent' => 'boolean',
            'type' => LogTypeEnum::class,
        ];
}
