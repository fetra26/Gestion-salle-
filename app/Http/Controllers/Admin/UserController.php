<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'direction']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        $users      = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles      = Role::orderBy('name')->get();
        $directions = Direction::orderBy('nom')->get();

        return view('admin.users.index', compact('users', 'roles', 'directions'));
    }

    public function create()
    {
        $roles      = Role::orderBy('name')->get();
        $directions = Direction::orderBy('nom')->get();
        return view('admin.users.create', compact('roles', 'directions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'role'         => ['required', 'exists:roles,name'],
            'direction_id' => ['required', 'exists:directions,id'],
        ], [
            'name.required'          => 'Le nom est obligatoire.',
            'email.required'         => 'L\'adresse email est obligatoire.',
            'email.unique'           => 'Cette adresse email est déjà utilisée.',
            'password.required'      => 'Le mot de passe est obligatoire.',
            'role.required'          => 'Le rôle est obligatoire.',
            'direction_id.required'  => 'La direction est obligatoire.',
            'direction_id.exists'    => 'La direction sélectionnée est invalide.',
        ]);

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'direction_id' => $validated['direction_id'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Utilisateur créé avec succès.'], 201);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        $roles      = Role::orderBy('name')->get();
        $directions = Direction::orderBy('nom')->get();
        $userRole   = $user->roles->first()?->name;
        return view('admin.users.edit', compact('user', 'roles', 'directions', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'         => ['required', 'exists:roles,name'],
            'direction_id' => ['required', 'exists:directions,id'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $validated = $request->validate($rules, [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique'   => 'Cette adresse email est déjà utilisée.',
            'role.required'  => 'Le rôle est obligatoire.',
        ]);

        $user->update([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'direction_id' => $validated['direction_id'] ?? null,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
