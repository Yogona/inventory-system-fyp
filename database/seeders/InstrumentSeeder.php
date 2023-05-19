<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Instrument::create([
            "name"          => "Item A.",
            "description"   => "Doing A.",
            "quantity"      => 200,
            "code"          => "AAA",
            "added_by"      => 1,
            "store_id"      => 1
        ]);
    }
}
