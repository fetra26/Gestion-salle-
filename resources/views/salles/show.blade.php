@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('salles.index') }}">Salles</a></li>
            <li class="breadcrumb-item active">{{ $salle->nom }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-building fs-4 text-primary"></i>
            <h4 class="mb-0 fw-semibold">{{ $salle->nom }}</h4>
            @if($salle->active)
            <span class="badge bg-success">Active</span>
            @else
            <span class="badge bg-secondary">Inactive</span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('salles.edit', $salle) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
            <a href="{{ route('salles.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom fw-medium">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informations
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted fw-normal">Capacité</dt>
                        <dd class="col-sm-7">
                            <i class="bi bi-people me-1 text-muted"></i>{{ $salle->capacite }} personnes
                        </dd>

                        <dt class="col-sm-5 text-muted fw-normal">Statut</dt>
                        <dd class="col-sm-7">
                            @if($salle->active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </dd>

                        @if($salle->description)
                        <dt class="col-sm-5 text-muted fw-normal">Description</dt>
                        <dd class="col-sm-7">{{ $salle->description }}</dd>
                        @endif

                        @if($salle->equipement)
                        <dt class="col-sm-5 text-muted fw-normal">Équipement</dt>
                        <dd class="col-sm-7">{{ $salle->equipement }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom fw-medium d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>Historique des réservations
                </div>
                <div class="card-body p-0">
                    @if($salle->reservations->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                            <small>Aucune réservation pour cette salle.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Date</th>
                                        <th>Demandeur</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salle->reservations->take(10) as $reservation)
                                    <tr>
                                        <td class="ps-3">{{ $reservation->date_debut->format('d/m/Y H:i') }}</td>
                                        <td>{{ $reservation->demandeur->name }}</td>
                                        <td><x-statut-badge :statut="$reservation->statut" /></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($salle->reservations->count() > 10)
                        <div class="px-3 py-2 text-muted" style="font-size:.8rem">
                            Affichage des 10 dernières sur {{ $salle->reservations->count() }} au total.
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
