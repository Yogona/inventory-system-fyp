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
            "username"       => "admin",
            "password"      => Hash::make("1234"),
            "role_id"       => 1,
        ]);

        User::create([
            "first_name"    => "John",
            "last_name"     => "Doe",
            "gender"        => "M",
            "phone"         => "0700112234",
            "email"         => "manager@localhost",
            "username"       => "manager",
            "password"      => Hash::make("1234"),
            "role_id"       => 2,
        ]);

        User::create([
            "first_name"    => "Jane",
            "last_name"     => "Doe",
            "gender"        => "F",
            "phone"         => "0700112235",
            "email"         => "keeper@localhost",
            "username"       => "keeper",
            "password"      => Hash::make("1234"),
            "role_id"       => 3,
        ]);
        User::create([
            "first_name"    => "Joseph",
            "last_name"     => "Owigo",
            "gender"        => "M",
            "phone"         => "0700112236",
            "email"         => "owigo@localhost",
            "username"       => "owigo",
            "password"      => Hash::make("1234"),
            "role_id"       => 4,
        ]);
        User::create([
            "first_name"    => "Gasper",
            "last_name"     => "Gidson",
            "gender"        => "M",
            "phone"         => "0700112237",
            "email"         => "gasper@localhost",
            "username"      => "gasper",
            "password"      => Hash::make("1234"),
            "role_id"       => 5,
        ]);
    }
}
