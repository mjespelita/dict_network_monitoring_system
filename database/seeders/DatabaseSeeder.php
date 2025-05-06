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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Mark Jason Espelita',
            'email' => 'mark.medivadigital@gmail.com',
            'role' => 'admin',
            'customers_id' => 0,
            'customers_users_id' => 0,
        ]);
    }
}
