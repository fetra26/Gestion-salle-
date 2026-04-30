<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Salle;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {}

    public function index(Request $request)
    {
        $query = Reservation::with(['salle', 'demandeur']);

        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('salle_id') && $request->salle_id) {
            $query->where('salle_id', $request->salle_id);
        }

        if ($request->has('date')) {
            $query->whereDate('date_debut', $request->date);
        }

        $reservations = $query->orderBy('date_debut', 'desc')->paginate(15);
        $salles = Salle::orderBy('nom')->get();

        return view('reservations.index', compact('reservations', 'salles'));
    }

    public function mesReservations(Request $request)
    {
        $user = Auth::user();
        $query = $user->reservations()->with(['salle', 'responsable']);

        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        $reservations = $query->orderBy('date_debut', 'desc')->paginate(15);

        return view('reservations.mes-reservations', compact('reservations'));
    }

    public function create()
    {
        $salles = Salle::where('active', true)->orderBy('nom')->get();
        return view('reservations.create', compact('salles'));
    }

    public function store(StoreReservationRequest $request)
    {
        $this->reservationService->creerDemande($request->validated(), Auth::user());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Votre demande de réservation a été soumise avec succès.'], 201);
        }

        return redirect()->route('reservations.mes-reservations')
            ->with('success', 'Votre demande de réservation a été soumise avec succès.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['salle', 'demandeur', 'responsable']);
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        
        $salles = Salle::where('active', true)->orderBy('nom')->get();
        return view('reservations.edit', compact('reservation', 'salles'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        
        $this->reservationService->mettreAJour($reservation->id, $request->validated(), Auth::user());

        return redirect()->route('reservations.mes-reservations')
            ->with('success', 'Réservation mise à jour avec succès.');
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);
        
        $this->reservationService->supprimer($reservation->id, Auth::user());

        return redirect()->route('reservations.mes-reservations')
            ->with('success', 'Réservation supprimée avec succès.');
    }

    // Actions du responsable
    public function aValider()
    {
        $reservations = Reservation::with(['salle', 'demandeur'])
            ->where('statut', Reservation::STATUT_EN_ATTENTE)
            ->orderBy('date_debut', 'asc')
            ->paginate(15);

        return view('reservations.a-valider', compact('reservations'));
    }

    public function valider(Request $request, Reservation $reservation)
    {
        $request->validate([
            'motif' => 'nullable|string|max:1000',
        ]);

        $this->reservationService->valider($reservation->id, Auth::user(), $request->motif);

        return back()->with('success', 'Réservation confirmée avec succès.');
    }

    public function refuser(Request $request, Reservation $reservation)
    {
        $request->validate([
            'motif' => 'required|string|max:1000',
        ], [
            'motif.required' => 'Le motif est obligatoire pour refuser une réservation.',
        ]);

        $this->reservationService->refuser($reservation->id, Auth::user(), $request->motif);

        return back()->with('success', 'Réservation refusée.');
    }

    public function annuler(Request $request, Reservation $reservation)
    {
        $request->validate([
            'motif' => 'required|string|max:1000',
        ], [
            'motif.required' => 'Le motif est obligatoire pour annuler une réservation.',
        ]);

        $this->reservationService->annuler($reservation->id, Auth::user(), $request->motif);

        return back()->with('success', 'Réservation annulée.');
    }

    public function checkDisponibilite(Request $request): JsonResponse
    {
        $request->validate([
            'salle_id'   => 'required|integer|exists:salles,id',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
            'except_id'  => 'nullable|integer',
        ]);

        try {
            $this->reservationService->verifierConflit(
                (int) $request->salle_id,
                $request->date_debut,
                $request->date_fin,
                $request->except_id ? (int) $request->except_id : null
            );
            return response()->json(['disponible' => true]);
        } catch (ValidationException) {
            return response()->json(['disponible' => false]);
        }
    }

    // Réservation directe par le responsable
    public function createDirecte()
    {
        $salles = Salle::where('active', true)->orderBy('nom')->get();
        return view('reservations.create-directe', compact('salles'));
    }

    public function storeDirecte(StoreReservationRequest $request)
    {
        $this->reservationService->creerReservationDirecte($request->validated(), Auth::user());

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation directe créée avec succès.');
    }
}