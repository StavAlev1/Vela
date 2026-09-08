<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        Post::factory()
            ->count(50)
            ->create()
            ->each(function (Post $post) {
                $post->tags()->attach(
                    Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')
                );

                // Leave some posts uncategorized to exercise that path too.
                if (rand(1, 10) > 2) {
                    $post->update([
                        'category_id' => Category::inRandomOrder()->value('id'),
                    ]);
                }

                $post->update(['views' => rand(0, 500)]);
            });
    }
}
