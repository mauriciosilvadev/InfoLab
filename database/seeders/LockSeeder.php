<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use App\Models\Lock;
use Illuminate\Database\Seeder;

class LockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laboratories = Laboratory::all();

        foreach ($laboratories as $laboratory) {
            Lock::create([
                'asset_number' => rand(100000, 999999) . '_fake',
                'laboratory_id' => $laboratory->id,
            ]);
        }
    }
}
