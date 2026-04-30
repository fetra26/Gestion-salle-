<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function index()
    {
        $directions = Direction::withCount('users')->orderBy('nom')->get();
        return view('admin.directions.index', compact('directions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'         => ['required', 'string', 'max:255', 'unique:directions,nom'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.unique'   => 'Ce nom de direction existe déjà.',
        ]);

        $direction = Direction::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message'   => 'Direction créée avec succès.',
                'direction' => $direction->load('users'),
            ], 201);
        }

        return redirect()->route('admin.directions.index')
            ->with('success', 'Direction créée avec succès.');
    }

    public function update(Request $request, Direction $direction)
    {
        $validated = $request->validate([
            'nom'         => ['required', 'string', 'max:255', 'unique:directions,nom,' . $direction->id],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.unique'   => 'Ce nom de direction existe déjà.',
        ]);

        $direction->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Direction mise à jour avec succès.']);
        }

        return redirect()->route('admin.directions.index')
            ->with('success', 'Direction mise à jour avec succès.');
    }

    public function destroy(Direction $direction)
    {
        $direction->users()->update(['direction_id' => null]);
        $direction->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Direction supprimée.']);
        }

        return redirect()->route('admin.directions.index')
            ->with('success', 'Direction supprimée. Les utilisateurs ont été désaffectés.');
    }
}
