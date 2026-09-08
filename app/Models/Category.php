<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            // Keep the slug in sync with the name unless someone set it explicitly.
            if (empty($category->slug) || $category->isDirty('name')) {
                $base = Str::slug($category->name) ?: 'category';
                $slug = $base;
                $suffix = 1;

                while (
                    static::where('slug', $slug)
                        ->when($category->exists, fn ($q) => $q->where('id', '!=', $category->id))
                        ->exists()
                ) {
                    $slug = "{$base}-{$suffix}";
                    $suffix++;
                }

                $category->slug = $slug;
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
