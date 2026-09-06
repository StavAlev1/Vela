<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // A known admin account you can always log into
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin12345'),
        ])->assignRole('admin');

        // A known editor account
        User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
        ])->syncRoles(['editor']);

        // A batch of regular fake users
        User::factory()->count(20)->create();
    }
}
