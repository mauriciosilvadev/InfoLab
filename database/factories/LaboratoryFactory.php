<?php

namespace Database\Factories;

use App\Models\Laboratory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Laboratory>
 */
class LaboratoryFactory extends Factory
{
    protected $model = Laboratory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company() . ' Lab',
            'building' => 'Prédio ' . $this->faker->randomDigitNotNull(),
            'computers_count' => $this->faker->numberBetween(0, 60),
            'photos' => [],
            'softwares' => $this->faker->randomElements(
                ['LibreOffice', 'MATLAB', 'AutoCAD', 'Python', 'RStudio', 'Unity'],
                $this->faker->numberBetween(0, 3)
            ),
        ];
    }
}
