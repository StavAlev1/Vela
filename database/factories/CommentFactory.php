<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'body' => fake()->paragraph(1),
            'commentable_id' => Post::inRandomOrder()->first()?->id ?? Post::factory(),
            'commentable_type' => Post::class,
        ];
    }
}