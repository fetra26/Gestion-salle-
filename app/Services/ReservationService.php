<?php

namespace App\Services;

use App\Mail\ReservationAnnulee;
use App\Mail\ReservationConfirmee;
use App\Mail\ReservationRefusee;
use App\Mail\ReservationSoumise;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    /**
     * Crée une demande de réservation.
     */
    public function creerDemande(array $donnees, User $demandeur): Reservation
    {
        return DB::transaction(function () use ($donnees, $demandeur) {
            $this->verifierConflit($donnees['salle_id'], $donnees['date_debut'], $donnees['date_fin']);

            $reservation = Reservation::create([
                'salle_id' => $donnees['salle_id'],
                'demandeur_id' => $demandeur->id,
                'date_debut' => $donnees['date_debut'],
                'date_fin' => $donnees['date_fin'],
                'statut' => Reservation::STATUT_EN_ATTENTE,
                'description' => $donnees['description'] ?? null,
            ]);

            Mail::to($demandeur->email)->queue(new ReservationSoumise($reservation->load(['salle', 'demandeur'])));

            return $reservation;
        });
    }

    /**
     * Crée une réservation directe (par un responsable).
     */
    public function creerReservationDirecte(array $donnees, User $responsable): Reservation
    {
        $this->verifierConflit($donnees['salle_id'], $donnees['date_debut'], $donnees['date_fin']);

        return DB::transaction(function () use ($donnees, $responsable) {
            $reservation = Reservation::create([
                'salle_id' => $donnees['salle_id'],
                'demandeur_id' => $donnees['demandeur_id'],
                'responsable_id' => $responsable->id,
                'date_debut' => $donnees['date_debut'],
                'date_fin' => $donnees['date_fin'],
                'statut' => Reservation::STATUT_CONFIRMEE,
                'motif' => $donnees['motif'] ?? 'Réservation directe créée par le responsable',
                'description' => $donnees['description'] ?? null,
            ]);

            $this->rejeterDemandesChevauchantes(
                $donnees['salle_id'],
                $donnees['date_debut'],
                $donnees['date_fin'],
                $reservation->id
            );

            return $reservation;
        });
    }

    /**
     * Valide une demande de réservation.
     */
    public function valider(int $reservationId, User $responsable, string $motif = null): Reservation
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (!$reservation->peutEtreConfirmee()) {
            throw ValidationException::withMessages([
                'reservation' => ['Cette réservation ne peut pas être confirmée.'],
            ]);
        }

        $this->verifierConflit($reservation->salle_id, $reservation->date_debut, $reservation->date_fin, $reservationId);

        return DB::transaction(function () use ($reservation, $responsable, $motif) {
            $reservation->update([
                'statut' => Reservation::STATUT_CONFIRMEE,
                'responsable_id' => $responsable->id,
                'motif' => $motif,
            ]);

            $this->rejeterDemandesChevauchantes(
                $reservation->salle_id,
                $reservation->date_debut,
                $reservation->date_fin,
                $reservation->id
            );

            $fresh = $reservation->fresh(['salle', 'demandeur', 'responsable']);
            Mail::to($fresh->demandeur->email)->queue(new ReservationConfirmee($fresh));

            return $fresh;
        });
    }

    /**
     * Refuse une demande de réservation.
     */
    public function refuser(int $reservationId, User $responsable, string $motif): Reservation
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (!$reservation->peutEtreRefusee()) {
            throw ValidationException::withMessages([
                'reservation' => ['Cette réservation ne peut pas être refusée.'],
            ]);
        }

        if (empty($motif)) {
            throw ValidationException::withMessages([
                'motif' => ['Le motif est obligatoire pour refuser une réservation.'],
            ]);
        }

        $reservation->update([
            'statut' => Reservation::STATUT_REFUSEE,
            'responsable_id' => $responsable->id,
            'motif' => $motif,
        ]);

        $fresh = $reservation->fresh(['salle', 'demandeur', 'responsable']);
        Mail::to($fresh->demandeur->email)->queue(new ReservationRefusee($fresh));

        return $fresh;
    }

    /**
     * Annule une réservation.
     */
    public function annuler(int $reservationId, User $responsable, string $motif): Reservation
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (!$reservation->peutEtreAnnulee()) {
            throw ValidationException::withMessages([
                'reservation' => ['Cette réservation ne peut pas être annulée.'],
            ]);
        }

        if (empty($motif)) {
            throw ValidationException::withMessages([
                'motif' => ['Le motif est obligatoire pour annuler une réservation.'],
            ]);
        }

        $reservation->update([
            'statut' => Reservation::STATUT_ANNULEE,
            'responsable_id' => $responsable->id,
            'motif' => $motif,
        ]);

        $fresh = $reservation->fresh(['salle', 'demandeur', 'responsable']);
        Mail::to($fresh->demandeur->email)->queue(new ReservationAnnulee($fresh));

        return $fresh;
    }

    /**
     * Met à jour une réservation (pour le demandeur).
     */
    public function mettreAJour(int $reservationId, array $donnees, User $demandeur): Reservation
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->demandeur_id !== $demandeur->id) {
            throw ValidationException::withMessages([
                'reservation' => ['Vous n\'êtes pas autorisé à modifier cette réservation.'],
            ]);
        }

        if ($reservation->statut !== Reservation::STATUT_EN_ATTENTE) {
            throw ValidationException::withMessages([
                'reservation' => ['Seules les demandes en attente peuvent être modifiées.'],
            ]);
        }

        $this->verifierConflit($donnees['salle_id'] ?? $reservation->salle_id, $donnees['date_debut'] ?? $reservation->date_debut, $donnees['date_fin'] ?? $reservation->date_fin, $reservationId);

        $reservation->update(array_filter([
            'salle_id' => $donnees['salle_id'] ?? null,
            'date_debut' => $donnees['date_debut'] ?? null,
            'date_fin' => $donnees['date_fin'] ?? null,
            'description' => $donnees['description'] ?? null,
        ], fn($v) => $v !== null));

        return $reservation->fresh();
    }

    /**
     * Supprime une réservation (par le demandeur).
     */
    public function supprimer(int $reservationId, User $demandeur): void
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->demandeur_id !== $demandeur->id) {
            throw ValidationException::withMessages([
                'reservation' => ['Vous n\'êtes pas autorisé à supprimer cette réservation.'],
            ]);
        }

        if ($reservation->statut !== Reservation::STATUT_EN_ATTENTE) {
            throw ValidationException::withMessages([
                'reservation' => ['Seules les demandes en attente peuvent être supprimées.'],
            ]);
        }

        $reservation->delete();
    }

    /**
     * Vérifie s'il y a un conflit avec une réservation confirmée.
     */
    public function verifierConflit(int $salleId, $dateDebut, $dateFin, ?int $exceptId = null): void
    {
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);

        $query = Reservation::where('salle_id', $salleId)
            ->where('statut', Reservation::STATUT_CONFIRMEE)
            ->where(function ($q) use ($debut, $fin) {
                $q->where(function ($q) use ($debut, $fin) {
                    $q->where('date_debut', '<', $fin)
                      ->where('date_fin', '>', $debut);
                });
            })
            ->lockForUpdate();

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'reservation' => ['Cette salle est déjà réservée pour ce créneau horaire.'],
            ]);
        }
    }

    /**
     * Rejette automatiquement les demandes en attente qui chevauchent une réservation confirmée.
     */
    protected function rejeterDemandesChevauchantes(int $salleId, $dateDebut, $dateFin, int $reservationId): void
    {
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);

        Reservation::where('salle_id', $salleId)
            ->where('statut', Reservation::STATUT_EN_ATTENTE)
            ->where('id', '!=', $reservationId)
            ->where(function ($q) use ($debut, $fin) {
                $q->whereBetween('date_debut', [$debut, $fin])
                    ->orWhereBetween('date_fin', [$debut, $fin])
                    ->orWhere(function ($q) use ($debut, $fin) {
                        $q->where('date_debut', '<=', $debut)
                            ->where('date_fin', '>=', $fin);
                    });
            })
            ->update([
                'statut' => Reservation::STATUT_REFUSEE,
                'motif' => 'Demande automatiquement refusée car une autre réservation a été confirmée sur ce créneau.',
            ]);
    }

    /**
     * Récupère les réservations pour le calendrier.
     */
    public function getReservationsPourCalendrier(?int $salleId = null, ?string $statut = null)
    {
        $query = Reservation::with(['salle', 'demandeur']);

        if ($salleId) {
            $query->where('salle_id', $salleId);
        }

        if ($statut) {
            $query->where('statut', $statut);
        }

        return $query->get()->map(function ($reservation) {
            $couleur = match ($reservation->statut) {
                Reservation::STATUT_EN_ATTENTE => '#ffc107',
                Reservation::STATUT_CONFIRMEE => '#28a745',
                Reservation::STATUT_REFUSEE => '#dc3545',
                Reservation::STATUT_ANNULEE => '#6c757d',
                Reservation::STATUT_TERMINEE => '#17a2b8',
                default => '#6c757d',
            };

            return [
                'id' => $reservation->id,
                'title' => $reservation->salle->nom . ' - ' . $reservation->demandeur->name,
                'start' => $reservation->date_debut->format('Y-m-d\TH:i:s'),
                'end'   => $reservation->date_fin->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $couleur,
                'borderColor' => $couleur,
                'extendedProps' => [
                    'statut' => $reservation->statut,
                    'salle' => $reservation->salle->nom,
                    'demandeur' => $reservation->demandeur->name,
                    'description' => $reservation->description,
                ],
            ];
        });
    }

    /**
     * Récupère les statistiques pour le dashboard.
     */
    public function getStatistiques(): array
    {
        $aujourdhui = Carbon::today();
        $finSemaine = Carbon::today()->endOfWeek();

        return [
            'total_reservations' => Reservation::count(),
            'reservations_en_attente' => Reservation::where('statut', Reservation::STATUT_EN_ATTENTE)->count(),
            'reservations_confirmees' => Reservation::where('statut', Reservation::STATUT_CONFIRMEE)->count(),
            'reservations_aujourdhui' => Reservation::where('statut', Reservation::STATUT_CONFIRMEE)
                ->whereDate('date_debut', $aujourdhui)
                ->count(),
            'reservations_semaine' => Reservation::where('statut', Reservation::STATUT_CONFIRMEE)
                ->whereBetween('date_debut', [$aujourdhui, $finSemaine])
                ->count(),
            'taux_validation' => $this->calculerTauxValidation(),
            'reservations_par_salle' => $this->getReservationsParSalle(),
            'reservations_par_mois' => $this->getReservationsParMois(),
        ];
    }

    protected function calculerTauxValidation(): float
    {
        $total = Reservation::whereIn('statut', [Reservation::STATUT_CONFIRMEE, Reservation::STATUT_REFUSEE])->count();
        
        if ($total === 0) {
            return 0;
        }

        $confirmees = Reservation::where('statut', Reservation::STATUT_CONFIRMEE)->count();
        
        return round(($confirmees / $total) * 100, 1);
    }

    protected function getReservationsParSalle(): array
    {
        return Reservation::where('statut', Reservation::STATUT_CONFIRMEE)
            ->with('salle')
            ->get()
            ->groupBy('salle_id')
            ->map(fn($reservations, $salleId) => [
                'salle' => $reservations->first()->salle->nom,
                'count' => $reservations->count(),
            ])
            ->values()
            ->toArray();
    }

    protected function getReservationsParMois(): array
    {
        $reservations = Reservation::where('statut', '!=', Reservation::STATUT_EN_ATTENTE)
            ->selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        $result = array_fill(1, 12, 0);
        foreach ($reservations as $r) {
            $result[$r->mois] = $r->total;
        }

        return $result;
    }
}