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
            'email' => config('init.email'),
            'email_verified_at' => now(),
            'password' => Hash::make(config('init.password')),
            'name' => 'Adrema Benutzer',
        ]);
    }
}
