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
        // مستخدم أدمن افتراضي (username: admin / password: admin12345)
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'email' => 'admin@mosque.local',
                'password' => 'admin12345', // يُشفّر تلقائياً عبر cast الموديل
            ]
        );
    }
}
