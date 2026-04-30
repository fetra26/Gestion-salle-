<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalleRequest;
use App\Http\Requests\UpdateSalleRequest;
use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function index()
    {
        $salles = Salle::orderBy('nom')->get();
        return view('salles.index', compact('salles'));
    }

    public function create()
    {
        return view('salles.create');
    }

    public function store(StoreSalleRequest $request)
    {
        Salle::create($request->validated());
        
        return redirect()->route('salles.index')
            ->with('success', 'Salle créée avec succès.');
    }

    public function show(Salle $salle)
    {
        $salle->load(['reservations' => function ($query) {
            $query->orderBy('date_debut', 'desc');
        }]);
        
        return view('salles.show', compact('salle'));
    }

    public function edit(Salle $salle)
    {
        return view('salles.edit', compact('salle'));
    }

    public function update(UpdateSalleRequest $request, Salle $salle)
    {
        $salle->update($request->validated());
        
        return redirect()->route('salles.index')
            ->with('success', 'Salle mise à jour avec succès.');
    }

    public function destroy(Salle $salle)
    {
        if ($salle->reservations()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette salle car elle contient des réservations.');
        }
        
        $salle->delete();
        
        return redirect()->route('salles.index')
            ->with('success', 'Salle supprimée avec succès.');
    }
}