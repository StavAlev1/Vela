<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Support\Facades\Storage;


class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 1. FILLABLE — which fields can be mass-assigned
     */
    protected $fillable = ['title', 'content', 'user_id'];

    /**
     * 2. HIDDEN — fields excluded when the model is converted to JSON/array
     * (useful for sensitive fields you never want exposed in an API response)
     */
    protected $hidden = ['internal_notes'];

    /**
     * 3. CASTS — automatically convert DB values to PHP types
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'metadata' => 'array', // stores/reads JSON column as a PHP array
    ];

    /**
     * 4. DEFAULT VALUES for new instances
     */
    protected $attributes = [
        'is_published' => false,
    ];

    protected static function booted(): void
    {
        static::forceDeleted(function (Post $post) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
        });
    }

    /**
     * 5. RELATIONSHIPS
     */

    // A post belongs to one author (User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Replaces the old hasMany() version
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * 6. ACCESSORS — computed/transformed values when reading
     */
    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn() => substr($this->content, 0, 100) . '...',
        );
    }

    /**
     * 7. MUTATORS — transform values before saving
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ucfirst($value), // capitalize title before saving
        );
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }


    /**
     * Accessor — returns the full public URL for the featured image,
     * or null if no image was uploaded.
     */
    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->featured_image
                ? asset('storage/' . $this->featured_image)
                : null,
        );
    }

    /**
     * 8. QUERY SCOPES — reusable query shortcuts
     */

    #[Scope]
    public function byUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    #[Scope]
    public function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
