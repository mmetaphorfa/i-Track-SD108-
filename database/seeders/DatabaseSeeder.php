<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Demo Admin
        User::factory()->create([
            'username' => '999999999999',
            'full_name' => 'Admin i-Track',
            'email' => 'admin@itrack-my.com',
            'phone' => '011987654321',
            'user_role' => 'both',
            'admin_role' => 'admin',
            'status' => 'active',
        ]);
        // User::factory(1000)->create();
    }
}
