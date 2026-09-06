<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,   // roles must exist BEFORE assigning them to users
            TagSeeder::class,   // tags before posts, since posts attach to them
            UserSeeder::class,   // users must exist BEFORE posts reference them
            PostSeeder::class,   // posts must exist BEFORE comments reference them
            CommentSeeder::class,
        ]);
    }
}
