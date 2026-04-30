<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {}

    public function index()
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['responsable', 'admin'])) {
            return redirect()->route('reservations.mes-reservations');
        }
        $statistiques = $this->reservationService->getStatistiques();
        
        $mesReservations = $user->reservations()
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->where('date_fin', '>=', now())
            ->orderBy('date_debut')
            ->limit(5)
            ->get();

        $reservationsAValider = [];
        if ($user->peutValider()) {
            $reservationsAValider = \App\Models\Reservation::where('statut', 'en_attente')
                ->orderBy('date_debut')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('statistiques', 'mesReservations', 'reservationsAValider'));
    }

    public function calendrier(Request $request)
    {
        $salles = Salle::where('active', true)->orderBy('nom')->get();
        $salleId = $request->get('salle_id');
        
        return view('calendrier', compact('salles', 'salleId'));
    }

    public function apiReservations(Request $request)
    {
        $salleId = $request->get('salle_id');
        $statut = $request->get('statut');
        
        $reservations = $this->reservationService->getReservationsPourCalendrier($salleId, $statut);
        
        return response()->json($reservations);
    }
}