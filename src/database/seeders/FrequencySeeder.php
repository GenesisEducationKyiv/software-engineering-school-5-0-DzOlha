<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FrequencySeeder extends Seeder
{
    public function run(): void
    {
        $frequencies = [
            'Hourly' => 60,
            'Daily' => 1440
        ];

        foreach ($frequencies as $label => $minutes) {
            DB::table('frequencies')->updateOrInsert([
                'name' => $label,
                'interval_minutes' => $minutes,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
