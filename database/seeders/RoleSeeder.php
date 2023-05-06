<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([//1
            "name"          => "Admin",
            "description"   => "Administrates the whole system, this is the super user."
        ]);

        Role::create([//2
            "name"          => "Manager",
            "description"   => "Oversees tools reports."
        ]);

        Role::create([//3
            "name"          => "Store keeper",
            "description"   => "Manages tools in the stores."
        ]);

        Role::create([//4
            "name"          => "Leacturer",
            "description"   => "Lecturers and submits instruments requests for the C.R. to be allocated with."
        ]);

        Role::create([//5
            "name"          => "Class Representative",
            "description"   => "C.R. attends to the store keeper to be allocated with instruments requested by lecturer."
        ]);
    }
}
