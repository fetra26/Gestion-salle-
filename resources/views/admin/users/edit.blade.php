@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-person-gear text-primary"></i>Modifier l'utilisateur
        </h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width:680px">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')

            {{-- Section : Informations personnelles --}}
            <div class="card-header bg-light border-bottom px-4 py-2">
                <span class="fw-semibold text-secondary" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em">
                    <i class="bi bi-person me-1"></i>Informations personnelles
                </span>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label for="name" class="form-label fw-medium">
                            Nom complet <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $user->name) }}"
                               placeholder="Jean Dupont" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="email" class="form-label fw-medium">
                            Adresse email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $user->email) }}"
                               placeholder="jean.dupont@exemple.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Section : Affectation --}}
            <div class="card-header bg-light border-bottom border-top px-4 py-2">
                <span class="fw-semibold text-secondary" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em">
                    <i class="bi bi-diagram-3 me-1"></i>Affectation
                </span>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label for="role" class="form-label fw-medium">
                            Rôle <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror"
                                id="role" name="role" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                    {{ old('role', $userRole) == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="direction_id" class="form-label fw-medium">
                            Direction <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('direction_id') is-invalid @enderror"
                                id="direction_id" name="direction_id" required>
                            <option value="">Sélectionner…</option>
                            @foreach($directions as $direction)
                            <option value="{{ $direction->id }}"
                                    {{ old('direction_id', $user->direction_id) == $direction->id ? 'selected' : '' }}>
                                {{ $direction->nom }}
                            </option>
                            @endforeach
                        </select>
                        @error('direction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Section : Mot de passe --}}
            <div class="card-header bg-light border-bottom border-top px-4 py-2">
                <span class="fw-semibold text-secondary" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em">
                    <i class="bi bi-lock me-1"></i>Mot de passe
                </span>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>Laissez vide pour conserver le mot de passe actuel.
                </p>
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label for="password" class="form-label fw-medium">Nouveau mot de passe</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="password_confirmation" class="form-label fw-medium">Confirmer</label>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
            </div>

            {{-- Footer boutons --}}
            <div class="card-footer bg-transparent border-top px-4 py-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Enregistrer
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Annuler
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
