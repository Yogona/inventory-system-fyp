<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "first_name"    => "System",
            "last_name"     => "Admin",
            "gender"        => "M",
            "phone"         => "0700112233",
            "email"         => "admin@localhost",
            "user_id"       => "admin",
            "password"      => Hash::make("1234"),
            "role_id"       => 1,
        ]);
    }
}
