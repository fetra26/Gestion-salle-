@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item active">Mes réservations</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-journal-check text-primary"></i>Mes réservations
        </h4>
        <button type="button" class="btn btn-primary btn-sm"
                data-bs-toggle="modal" data-bs-target="#modalNouvelleReservation">
            <i class="bi bi-plus-circle me-1"></i>Nouvelle demande
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($reservations->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-3 opacity-50"></i>
                    <p class="mb-1">Vous n'avez aucune réservation.</p>
                    <button type="button" class="btn btn-primary btn-sm mt-2"
                            data-bs-toggle="modal" data-bs-target="#modalNouvelleReservation">
                        <i class="bi bi-plus-circle me-1"></i>Faire une demande
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Salle</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Statut</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                            <tr>
                                <td class="ps-3">
                                    <i class="bi bi-building me-1 text-muted"></i>{{ $reservation->salle->nom }}
                                </td>
                                <td>{{ $reservation->date_debut->format('d/m/Y H:i') }}</td>
                                <td>{{ $reservation->date_fin->format('d/m/Y H:i') }}</td>
                                <td><x-statut-badge :statut="$reservation->statut" /></td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('reservations.show', $reservation) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Voir
                                    </a>
                                    @if($reservation->statut === 'en_attente')
                                    <a href="{{ route('reservations.edit', $reservation) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil me-1"></i>Modifier
                                    </a>
                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer cette demande ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Supprimer
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2">{{ $reservations->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
