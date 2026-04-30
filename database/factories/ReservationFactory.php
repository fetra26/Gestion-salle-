<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Salle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $debut = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $fin   = (clone $debut)->modify('+2 hours');

        return [
            'salle_id'     => Salle::factory(),
            'demandeur_id' => User::factory(),
            'statut'       => Reservation::STATUT_EN_ATTENTE,
            'date_debut'   => $debut,
            'date_fin'     => $fin,
            'description'  => $this->faker->sentence(),
        ];
    }

    public function confirmee(): static
    {
        return $this->state(['statut' => Reservation::STATUT_CONFIRMEE]);
    }

    public function refusee(): static
    {
        return $this->state(['statut' => Reservation::STATUT_REFUSEE]);
    }
}
