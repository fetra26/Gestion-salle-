<?php

namespace Database\Factories;

use App\Models\Salle;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalleFactory extends Factory
{
    protected $model = Salle::class;

    public function definition(): array
    {
        return [
            'nom'        => 'Salle ' . $this->faker->unique()->word(),
            'capacite'   => $this->faker->numberBetween(5, 50),
            'description' => $this->faker->sentence(),
            'equipement' => 'Tableau blanc, vidéo-projecteur',
            'active'     => true,
        ];
    }
}
