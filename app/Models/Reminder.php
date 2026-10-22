<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    public const CHANNEL_TELEGRAM = 'telegram';

    public const CHANNEL_DATABASE = 'database';

    public const STATUS_PENDING = 'pending';

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $fillable = ['event_id', 'remind_at', 'channel'];

    protected $attributes = [
        'channel' => self::CHANNEL_TELEGRAM,
        'status' => self::STATUS_PENDING,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function casts(): array
    {
        return [
            'remind_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }
}
