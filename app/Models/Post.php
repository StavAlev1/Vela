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
use Illuminate\Support\Str;


class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 1. FILLABLE — which fields can be mass-assigned
     */
    protected $fillable = ['title', 'slug', 'content', 'user_id', 'category_id', 'metadata', 'featured_image'];

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

    /**
     * Use the slug in route()/URL generation instead of the numeric id,
     * e.g. /posts/my-first-post rather than /posts/17.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (Post $post) {
            // Only delete from local storage when it actually is a local
            // path — featured_image can also hold a full external URL
            // (e.g. hot-linked stock photos), which there's nothing to clean up.
            if ($post->featured_image && ! $post->hasExternalImage()) {
                Storage::disk('public')->delete($post->featured_image);
            }
        });

        static::saving(function (Post $post) {
            // Auto-generate a unique slug from the title unless one was set
            // explicitly. Existing posts keep their slug even if the title
            // changes later, so published links never break.
            if (empty($post->slug)) {
                $base = Str::slug($post->title) ?: 'post';
                $slug = $base;
                $suffix = 1;

                while (
                    static::withTrashed()
                        ->where('slug', $slug)
                        ->when($post->exists, fn ($q) => $q->where('id', '!=', $post->id))
                        ->exists()
                ) {
                    $slug = "{$base}-{$suffix}";
                    $suffix++;
                }

                $post->slug = $slug;
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
     * or null if no image was set.
     *
     * featured_image holds either a path on the local "public" disk
     * (real uploads, via the post form) or a full external URL (e.g.
     * hot-linked stock photos from the backfill command) — this returns
     * the right thing either way.
     */
    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => match (true) {
                empty($this->featured_image) => null,
                $this->hasExternalImage() => $this->featured_image,
                default => asset('storage/' . $this->featured_image),
            },
        );
    }

    /**
     * True when featured_image is a full external URL rather than a path
     * on our own "public" disk.
     */
    public function hasExternalImage(): bool
    {
        return (bool) $this->featured_image && Str::startsWith($this->featured_image, ['http://', 'https://']);
    }

    /**
     * SEO title — falls back to the post title when no custom one was set.
     * Stored inside the existing `metadata` JSON column rather than adding
     * new columns.
     */
    protected function metaTitle(): Attribute
    {
        return Attribute::make(
            get: fn() => data_get($this->metadata, 'meta_title') ?: $this->title,
        );
    }

    /**
     * SEO/OG description — falls back to a plain-text excerpt of the content.
     */
    protected function metaDescription(): Attribute
    {
        return Attribute::make(
            get: fn() => data_get($this->metadata, 'meta_description')
                ?: Str::limit(strip_tags($this->content), 155),
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

    #[Scope]
    public function search(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
        });
    }
}
