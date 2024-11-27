<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Emmanuel Oteng Wilson',
            'email' => 'hagioswilson@gmail.com',
            'password' => Hash::make('password')
        ]);
    }
}
