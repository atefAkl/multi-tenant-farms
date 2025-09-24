<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo tenant
        $tenant = Tenant::create([
            'id' => Tenant::generateNewId(),
            'name' => 'مزرعة النخيل التجريبية',
            'email' => 'demo@palms.com',
            'phone' => '+966501234567',
            'address' => 'الرياض، المملكة العربية السعودية',
            'subscription_plan' => 'premium',
            'subscription_status' => 'active',
            'data' => [
                'farm_count' => 5,
                'total_area' => 100.5,
                'specialization' => 'date_palm_farming'
            ]
        ]);

        // Create tenant domain
        $tenant->domains()->create([
            'domain' => 'demo.localhost'
        ]);

        // Create admin user for the tenant
        $tenant->run(function ($tenant) {
            User::create([
                'name' => 'مدير المزرعة',
                'email' => 'admin@demo-farm.com',
                'password' => Hash::make('password'),
                'phone' => '+966501234567',
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]);

            User::create([
                'name' => 'مهندس زراعي',
                'email' => 'engineer@demo-farm.com',
                'password' => Hash::make('password'),
                'phone' => '+966501234568',
                'role' => 'engineer',
                'tenant_id' => $tenant->id,
            ]);

            User::create([
                'name' => 'عامل',
                'email' => 'worker@demo-farm.com',
                'password' => Hash::make('password'),
                'phone' => '+966501234569',
                'role' => 'worker',
                'tenant_id' => $tenant->id,
            ]);
        });
    }
}
