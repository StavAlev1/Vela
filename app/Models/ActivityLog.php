<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

/**
 * A lightweight, self-contained audit trail — no external package required.
 *
 * Call ActivityLog::record() from wherever a notable admin/content action
 * happens (publishing a post, promoting a user, deleting a comment, ...).
 * It deliberately isn't a model observer: explicit call sites keep the log
 * meaningful instead of firing on every incidental attribute change.
 */
class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'subject_type', 'subject_id', 'action', 'description', 'properties'];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(string $action, string $description, ?Model $subject = null, array $properties = []): self
    {
        return static::create([
            'user_id' => Auth::id(),
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'action' => $action,
            'description' => $description,
            'properties' => $properties ?: null,
            'created_at' => now(),
        ]);
    }
}
