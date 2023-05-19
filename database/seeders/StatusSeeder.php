<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create([
            "name"          => "Pending",
            "description"   => "Waiting approval."
        ]);

        Status::create([
            "name"          => "Allocated",
            "description"   => "Has been assigned to."
        ]);

        Status::create([
            "name"          => "Returned",
            "description"   => "Has been delivered back to the store."
        ]);
    }
}
