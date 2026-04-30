@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item active">Toutes les réservations</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-list-ul fs-4 text-primary"></i>
        <h4 class="mb-0 fw-semibold">Toutes les réservations</h4>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label form-label-sm text-muted">Statut</label>
                    <select class="form-select form-select-sm" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente"  {{ request('statut') == 'en_attente'  ? 'selected' : '' }}>En attente</option>
                        <option value="confirmee"   {{ request('statut') == 'confirmee'   ? 'selected' : '' }}>Confirmée</option>
                        <option value="refusee"     {{ request('statut') == 'refusee'     ? 'selected' : '' }}>Refusée</option>
                        <option value="annulee"     {{ request('statut') == 'annulee'     ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label form-label-sm text-muted">Salle</label>
                    <select class="form-select form-select-sm" name="salle_id">
                        <option value="">Toutes les salles</option>
                        @foreach($salles as $salle)
                        <option value="{{ $salle->id }}" {{ request('salle_id') == $salle->id ? 'selected' : '' }}>
                            {{ $salle->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label form-label-sm text-muted">Date</label>
                    <input type="date" class="form-control form-control-sm" name="date" value="{{ request('date') }}">
                </div>
                <div class="col-sm-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Réinit.
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($reservations->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-search fs-1 d-block mb-3 opacity-50"></i>
                    <p class="mb-0">Aucune réservation trouvée pour ces critères.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width:50px">#</th>
                                <th>Salle</th>
                                <th>Demandeur</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Statut</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                            <tr>
                                <td class="ps-3 text-muted">{{ $reservation->id }}</td>
                                <td><i class="bi bi-building me-1 text-muted"></i>{{ $reservation->salle->nom }}</td>
                                <td><i class="bi bi-person me-1 text-muted"></i>{{ $reservation->demandeur->name }}</td>
                                <td>{{ $reservation->date_debut->format('d/m/Y H:i') }}</td>
                                <td>{{ $reservation->date_fin->format('d/m/Y H:i') }}</td>
                                <td><x-statut-badge :statut="$reservation->statut" /></td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('reservations.show', $reservation) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Voir
                                    </a>
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
