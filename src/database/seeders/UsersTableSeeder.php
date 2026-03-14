<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'seller1@example.com'],
            [
                'name' => 'ユーザー名',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'seller2@example.com'],
            [
                'name' => 'ユーザー名',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user3@example.com'],
            [
                'name' => 'ユーザー名',
                'password' => Hash::make('password'),
            ]
        );
    }
}