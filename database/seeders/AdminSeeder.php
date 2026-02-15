<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@keripikilil.com'],
            [
                'name' => 'Admin iLiL',
                'email' => 'admin@keripikilil.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
    }
}
