<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use Illuminate\Database\Seeder;

class LaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laboratories = [
            [
                'name' => 'Laboratório 5',
                'building' => 'Reuni',
                'computers_count' => 0,
                'photos' => [],
                'softwares' => [],
            ],
            [
                'name' => 'Laboratório 6',
                'building' => 'Reuni',
                'computers_count' => 0,
                'photos' => [],
                'softwares' => [],
            ],
            [
                'name' => 'Laboratório 7',
                'building' => 'Reuni',
                'computers_count' => 0,
                'photos' => [],
                'softwares' => [],
            ],
            [
                'name' => 'Laboratório 8',
                'building' => 'Reuni',
                'computers_count' => 0,
                'photos' => [],
                'softwares' => [],
            ],
            [
                'name' => 'Laboratório 1',
                'building' => 'ChiuChiu',
                'computers_count' => 0,
                'photos' => [],
                'softwares' => [],
            ],
            [
                'name' => 'Laboratório 2',
                'building' => 'ChiuChiu',
                'computers_count' => 0,
                'photos' => [],
                'softwares' => [],
            ],
            [
                'name' => 'Laboratório 3',
                'building' => 'ChiuChiu',
                'computers_count' => 0,
                'photos' => [],
                'softwares' => [],
            ],
        ];

        foreach ($laboratories as $laboratory) {
            Laboratory::create($laboratory);
        }
    }
}
