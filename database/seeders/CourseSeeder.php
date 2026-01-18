<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            'Agronomia',
            'Engenharia de Alimentos',
            'Engenharia Florestal',
            'Engenharia Industrial Madeireira',
            'Engenharia Química',
            'Medicina Veterinária',
            'Zootecnia',
            'Ciência da Computação',
            'Ciências Biológicas (Bacharelado)',
            'Ciências Biológicas (Licenciatura)',
            'Farmácia',
            'Física (Licenciatura)',
            'Geologia',
            'Matemática (Licenciatura)',
            'Nutrição',
            'Química (Licenciatura)',
            'Sistemas de Informação',
        ];

        foreach ($courses as $courseName) {
            Course::firstOrCreate([
                'name' => $courseName,
            ]);
        }
    }
}
