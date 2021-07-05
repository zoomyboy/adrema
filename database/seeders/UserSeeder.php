<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin')
        ]);
    }
}
