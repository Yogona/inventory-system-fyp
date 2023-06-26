<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create([
            "name"          => "Store A",
            "description"   => "Doing A.",
            "location"      => "Area A",
            "store_keeper"  => 3,
            // "department_id" => 1
        ]);
    }
}
