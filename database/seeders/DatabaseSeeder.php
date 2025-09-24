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
        // Create main tenant and demo data
        $this->call([
            TenantSeeder::class,
            PalmFarmSeeder::class,
        ]);

        // Create default admin user for the main database
        User::factory()->create([
            'name' => 'المدير العام',
            'email' => 'admin@palms.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);
    }
}
