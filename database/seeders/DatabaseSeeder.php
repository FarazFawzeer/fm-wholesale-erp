<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First admin account: set ADMIN_EMAIL / ADMIN_PASSWORD in .env on a live server
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@fmwholesale.com')],
            [
                'name' => 'FM Admin',
                'type' => 'Super Admin',
                'active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'remember_token' => Str::random(10),
            ]
        );

        // Sample data for demos only (set SEED_DEMO=false for a clean live system)
        if (filter_var(env('SEED_DEMO', true), FILTER_VALIDATE_BOOL)) {
            $this->call(DemoSeeder::class);
        }
    }
}
