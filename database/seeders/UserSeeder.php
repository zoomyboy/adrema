<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => env('USER_EMAIL', 'admin@example.com'),
            'email_verified_at' => now(),
            'password' => Hash::make(env('USER_PASSWORD', 'admin')),
            'name' => 'Adrema Benutzer',
        ]);
    }
}
