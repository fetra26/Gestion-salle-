@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item active">Salles</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-building text-primary"></i>Gestion des salles
        </h4>
        <a href="{{ route('salles.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une salle
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($salles->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-building fs-1 d-block mb-3 opacity-40"></i>
                    <p class="mb-1 fw-medium">Aucune salle enregistrée.</p>
                    <a href="{{ route('salles.create') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-plus-circle me-1"></i>Créer la première salle
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nom</th>
                                <th>Capacité</th>
                                <th>Équipement</th>
                                <th>Statut</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salles as $salle)
                            <tr>
                                <td class="ps-3">
                                    <i class="bi bi-building me-1 text-muted"></i>
                                    <strong>{{ $salle->nom }}</strong>
                                </td>
                                <td>
                                    <i class="bi bi-people me-1 text-muted"></i>{{ $salle->capacite }} pers.
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size:.875rem">
                                        {{ Str::limit($salle->equipement, 50) ?: '—' }}
                                    </span>
                                </td>
                                <td>
                                    @if($salle->active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                                    @else
                                    <span class="badge bg-secondary"><i class="bi bi-pause-circle me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('salles.show', $salle) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Voir
                                    </a>
                                    <a href="{{ route('salles.edit', $salle) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil me-1"></i>Modifier
                                    </a>
                                    <form method="POST" action="{{ route('salles.destroy', $salle) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer la salle « {{ $salle->nom }} » ?\nCette action est irréversible.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
