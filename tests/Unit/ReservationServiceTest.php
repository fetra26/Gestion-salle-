<?php

namespace Tests\Unit;

use App\Mail\ReservationAnnulee;
use App\Mail\ReservationConfirmee;
use App\Mail\ReservationRefusee;
use App\Mail\ReservationSoumise;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservationService $service;
    private User $demandeur;
    private User $responsable;
    private Salle $salle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ReservationService();

        $this->demandeur   = User::factory()->create();
        $this->responsable = User::factory()->create();
        $this->salle       = Salle::factory()->create(['active' => true]);

        Mail::fake();
    }

    // ─── creerDemande ───────────────────────────────────────────────────────

    public function test_creer_demande_cree_reservation_en_attente(): void
    {
        $reservation = $this->service->creerDemande([
            'salle_id'    => $this->salle->id,
            'date_debut'  => '2026-06-01 09:00:00',
            'date_fin'    => '2026-06-01 11:00:00',
            'description' => 'Réunion équipe',
        ], $this->demandeur);

        $this->assertDatabaseHas('reservations', [
            'id'           => $reservation->id,
            'statut'       => Reservation::STATUT_EN_ATTENTE,
            'demandeur_id' => $this->demandeur->id,
        ]);

        Mail::assertQueued(ReservationSoumise::class);
    }

    public function test_creer_demande_envoie_email_soumise(): void
    {
        $this->service->creerDemande([
            'salle_id'   => $this->salle->id,
            'date_debut' => '2026-06-02 14:00:00',
            'date_fin'   => '2026-06-02 16:00:00',
        ], $this->demandeur);

        Mail::assertQueued(ReservationSoumise::class, function ($mail) {
            return $mail->hasTo($this->demandeur->email);
        });
    }

    // ─── verifierConflit ────────────────────────────────────────────────────

    public function test_conflit_leve_exception_si_chevauchement(): void
    {
        // Réservation confirmée existante 10h-12h
        Reservation::factory()->create([
            'salle_id'   => $this->salle->id,
            'statut'     => Reservation::STATUT_CONFIRMEE,
            'date_debut' => '2026-06-10 10:00:00',
            'date_fin'   => '2026-06-10 12:00:00',
        ]);

        $this->expectException(ValidationException::class);

        // Nouvelle demande 11h-13h (chevauche)
        $this->service->creerDemande([
            'salle_id'   => $this->salle->id,
            'date_debut' => '2026-06-10 11:00:00',
            'date_fin'   => '2026-06-10 13:00:00',
        ], $this->demandeur);
    }

    public function test_pas_de_conflit_si_creneaux_adjacents(): void
    {
        Reservation::factory()->create([
            'salle_id'   => $this->salle->id,
            'statut'     => Reservation::STATUT_CONFIRMEE,
            'date_debut' => '2026-06-10 08:00:00',
            'date_fin'   => '2026-06-10 10:00:00',
        ]);

        // Créneau adjacent (début = fin de l'autre) — pas de chevauchement
        $reservation = $this->service->creerDemande([
            'salle_id'   => $this->salle->id,
            'date_debut' => '2026-06-10 10:00:00',
            'date_fin'   => '2026-06-10 12:00:00',
        ], $this->demandeur);

        $this->assertEquals(Reservation::STATUT_EN_ATTENTE, $reservation->statut);
    }

    public function test_pas_de_conflit_si_salle_differente(): void
    {
        $autreSalle = Salle::factory()->create();

        Reservation::factory()->create([
            'salle_id'   => $autreSalle->id,
            'statut'     => Reservation::STATUT_CONFIRMEE,
            'date_debut' => '2026-06-10 10:00:00',
            'date_fin'   => '2026-06-10 12:00:00',
        ]);

        $reservation = $this->service->creerDemande([
            'salle_id'   => $this->salle->id,
            'date_debut' => '2026-06-10 10:00:00',
            'date_fin'   => '2026-06-10 12:00:00',
        ], $this->demandeur);

        $this->assertEquals(Reservation::STATUT_EN_ATTENTE, $reservation->statut);
    }

    public function test_pas_de_conflit_avec_reservation_en_attente(): void
    {
        // Les réservations "en_attente" ne bloquent pas les nouvelles demandes
        Reservation::factory()->create([
            'salle_id'   => $this->salle->id,
            'statut'     => Reservation::STATUT_EN_ATTENTE,
            'date_debut' => '2026-06-10 10:00:00',
            'date_fin'   => '2026-06-10 12:00:00',
        ]);

        $reservation = $this->service->creerDemande([
            'salle_id'   => $this->salle->id,
            'date_debut' => '2026-06-10 10:00:00',
            'date_fin'   => '2026-06-10 12:00:00',
        ], $this->demandeur);

        $this->assertEquals(Reservation::STATUT_EN_ATTENTE, $reservation->statut);
    }

    // ─── valider ────────────────────────────────────────────────────────────

    public function test_valider_confirme_reservation_et_rejette_chevauchantes(): void
    {
        $r1 = Reservation::factory()->create([
            'salle_id'     => $this->salle->id,
            'demandeur_id' => $this->demandeur->id,
            'statut'       => Reservation::STATUT_EN_ATTENTE,
            'date_debut'   => '2026-07-01 09:00:00',
            'date_fin'     => '2026-07-01 11:00:00',
        ]);

        // Autre demande en chevauchement
        $r2 = Reservation::factory()->create([
            'salle_id'   => $this->salle->id,
            'statut'     => Reservation::STATUT_EN_ATTENTE,
            'date_debut' => '2026-07-01 10:00:00',
            'date_fin'   => '2026-07-01 12:00:00',
        ]);

        $this->service->valider($r1->id, $this->responsable);

        $this->assertEquals(Reservation::STATUT_CONFIRMEE, $r1->fresh()->statut);
        $this->assertEquals(Reservation::STATUT_REFUSEE, $r2->fresh()->statut);

        Mail::assertQueued(ReservationConfirmee::class);
    }

    public function test_valider_leve_exception_si_deja_confirmee(): void
    {
        $r = Reservation::factory()->create([
            'salle_id' => $this->salle->id,
            'statut'   => Reservation::STATUT_CONFIRMEE,
        ]);

        $this->expectException(ValidationException::class);
        $this->service->valider($r->id, $this->responsable);
    }

    // ─── refuser ────────────────────────────────────────────────────────────

    public function test_refuser_necessite_motif(): void
    {
        $r = Reservation::factory()->create([
            'salle_id' => $this->salle->id,
            'statut'   => Reservation::STATUT_EN_ATTENTE,
        ]);

        $this->expectException(ValidationException::class);
        $this->service->refuser($r->id, $this->responsable, '');
    }

    public function test_refuser_avec_motif_change_statut_et_envoie_email(): void
    {
        $r = Reservation::factory()->create([
            'salle_id'     => $this->salle->id,
            'demandeur_id' => $this->demandeur->id,
            'statut'       => Reservation::STATUT_EN_ATTENTE,
        ]);

        $this->service->refuser($r->id, $this->responsable, 'Salle déjà occupée');

        $this->assertEquals(Reservation::STATUT_REFUSEE, $r->fresh()->statut);
        $this->assertEquals('Salle déjà occupée', $r->fresh()->motif);

        Mail::assertQueued(ReservationRefusee::class);
    }

    // ─── annuler ────────────────────────────────────────────────────────────

    public function test_annuler_necessite_motif(): void
    {
        $r = Reservation::factory()->create([
            'salle_id' => $this->salle->id,
            'statut'   => Reservation::STATUT_CONFIRMEE,
        ]);

        $this->expectException(ValidationException::class);
        $this->service->annuler($r->id, $this->responsable, '');
    }

    public function test_annuler_avec_motif_change_statut_et_envoie_email(): void
    {
        $r = Reservation::factory()->create([
            'salle_id'     => $this->salle->id,
            'demandeur_id' => $this->demandeur->id,
            'statut'       => Reservation::STATUT_CONFIRMEE,
        ]);

        $this->service->annuler($r->id, $this->responsable, 'Travaux imprévus');

        $this->assertEquals(Reservation::STATUT_ANNULEE, $r->fresh()->statut);

        Mail::assertQueued(ReservationAnnulee::class);
    }

    // ─── auto-refus chevauchants ─────────────────────────────────────────────

    public function test_auto_refus_avec_motif_systeme(): void
    {
        $confirmee = Reservation::factory()->create([
            'salle_id'   => $this->salle->id,
            'statut'     => Reservation::STATUT_EN_ATTENTE,
            'date_debut' => '2026-08-01 09:00:00',
            'date_fin'   => '2026-08-01 11:00:00',
        ]);

        $autre = Reservation::factory()->create([
            'salle_id'   => $this->salle->id,
            'statut'     => Reservation::STATUT_EN_ATTENTE,
            'date_debut' => '2026-08-01 09:30:00',
            'date_fin'   => '2026-08-01 10:30:00',
        ]);

        $this->service->valider($confirmee->id, $this->responsable);

        $autreRefusee = $autre->fresh();
        $this->assertEquals(Reservation::STATUT_REFUSEE, $autreRefusee->statut);
        $this->assertNotEmpty($autreRefusee->motif);
    }
}
