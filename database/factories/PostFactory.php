<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'content' => fake()->paragraphs(4, true),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'is_published' => fake()->boolean(80), // 80% chance of being published
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
