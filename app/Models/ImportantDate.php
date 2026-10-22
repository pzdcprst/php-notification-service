<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportantDate extends Model
{
    protected $fillable = ['contact_id', 'title', 'date', 'repeat_yearly'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'repeat_yearly' => 'boolean',
            'last_notified_year' => 'integer',
        ];
    }
}
