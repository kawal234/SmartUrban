<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrafficZone;
use App\Models\TransportRoute;
use App\Models\UtilityUsage;
use App\Models\LogisticsRoute;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create transport routes
        TransportRoute::factory(5)->create();

        // Create utility usages
        UtilityUsage::factory(5)->create([
            'utility_type' => 'electricity',
            'status' => 'normal'
        ]);

        UtilityUsage::factory(3)->create([
            'utility_type' => 'water',
            'status' => 'high'
        ]);

        UtilityUsage::factory(2)->create([
            'utility_type' => 'gas',
            'status' => 'critical'
        ]);

        // Create traffic zones
        TrafficZone::factory(3)->create();

        // Create logistics routes
        LogisticsRoute::factory(5)->create();
    }
}
