<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()
            ->whereIn('email', ['admin@gmail.com', 'rayakanmomen0@gmail.com'])
            ->update(['email' => 'admin@rayakanmomen.com']);

        User::updateOrCreate(
            ['email' => 'admin@rayakanmomen.com'],
            [
                'name' => 'Admin',
                'password' => 'Rama Sat119',
                'email_verified_at' => now(),
            ]
        );
    }
}
