@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            @if(Auth::user()->peutValider())
            <li class="breadcrumb-item"><a href="{{ route('reservations.index') }}">Réservations</a></li>
            @else
            <li class="breadcrumb-item"><a href="{{ route('reservations.mes-reservations') }}">Mes réservations</a></li>
            @endif
            <li class="breadcrumb-item active">Réservation #{{ $reservation->id }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-calendar2-event fs-4 text-primary"></i>
        <h4 class="mb-0 fw-semibold">Détails de la réservation</h4>
        <x-statut-badge :statut="$reservation->statut" />
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom fw-medium">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informations
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-md-4 text-muted fw-normal">Salle</dt>
                        <dd class="col-md-8 fw-semibold">
                            <i class="bi bi-building me-1 text-muted"></i>{{ $reservation->salle->nom }}
                        </dd>

                        <dt class="col-md-4 text-muted fw-normal">Demandeur</dt>
                        <dd class="col-md-8">
                            <i class="bi bi-person me-1 text-muted"></i>{{ $reservation->demandeur->name }}
                        </dd>

                        <dt class="col-md-4 text-muted fw-normal">Début</dt>
                        <dd class="col-md-8 fw-semibold">
                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                            {{ $reservation->date_debut->translatedFormat('l d F Y') }}
                            à {{ $reservation->date_debut->format('H:i') }}
                        </dd>

                        <dt class="col-md-4 text-muted fw-normal">Fin</dt>
                        <dd class="col-md-8 fw-semibold">
                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                            {{ $reservation->date_fin->translatedFormat('l d F Y') }}
                            à {{ $reservation->date_fin->format('H:i') }}
                        </dd>

                        <dt class="col-md-4 text-muted fw-normal">Durée</dt>
                        <dd class="col-md-8">
                            @php
                                $diff = $reservation->date_debut->diff($reservation->date_fin);
                                $duree = '';
                                if ($diff->h > 0) $duree .= $diff->h . 'h';
                                if ($diff->i > 0) $duree .= $diff->i . 'min';
                                if (!$duree) $duree = '0min';
                            @endphp
                            <i class="bi bi-clock me-1 text-muted"></i>{{ $duree }}
                        </dd>

                        @if($reservation->responsable_id)
                        <dt class="col-md-4 text-muted fw-normal">Responsable</dt>
                        <dd class="col-md-8">
                            <i class="bi bi-person-check me-1 text-muted"></i>{{ $reservation->responsable->name }}
                        </dd>
                        @endif

                        @if($reservation->description)
                        <dt class="col-md-4 text-muted fw-normal">Description</dt>
                        <dd class="col-md-8">{{ $reservation->description }}</dd>
                        @endif

                        @if($reservation->motif)
                        <dt class="col-md-4 text-muted fw-normal">Motif</dt>
                        <dd class="col-md-8">
                            <span class="text-{{ in_array($reservation->statut, ['refusee','annulee']) ? 'danger' : 'success' }}">
                                {{ $reservation->motif }}
                            </span>
                        </dd>
                        @endif

                        <dt class="col-md-4 text-muted fw-normal">Créée le</dt>
                        <dd class="col-md-8 text-muted">{{ $reservation->created_at->format('d/m/Y à H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom fw-medium">
                    <i class="bi bi-lightning me-2 text-primary"></i>Actions
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    @if(Auth::user()->peutValider() && $reservation->statut === 'en_attente')
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#validerModal">
                        <i class="bi bi-check-circle me-2"></i>Valider la demande
                    </button>
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#refuserModal">
                        <i class="bi bi-x-circle me-2"></i>Refuser la demande
                    </button>
                    @endif

                    @if(Auth::user()->peutValider() && in_array($reservation->statut, ['en_attente', 'confirmee']))
                    <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#annulerModal">
                        <i class="bi bi-slash-circle me-2"></i>Annuler la réservation
                    </button>
                    @endif

                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Valider --}}
@if(Auth::user()->peutValider() && $reservation->statut === 'en_attente')
<div class="modal fade" id="validerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('reservations.valider', $reservation) }}">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle text-success"></i>Valider la réservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="text-muted">Vous êtes sur le point de confirmer cette demande de réservation.</p>
                    <div class="mb-3">
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" name="motif" rows="2" placeholder="Message pour le demandeur…"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="refuserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('reservations.refuser', $reservation) }}">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="bi bi-x-circle text-danger"></i>Refuser la demande
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label class="form-label">Motif du refus <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('motif') is-invalid @enderror"
                                  name="motif" rows="3" required placeholder="Indiquez la raison du refus…"></textarea>
                        @error('motif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Refuser</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Annuler --}}
@if(Auth::user()->peutValider() && in_array($reservation->statut, ['en_attente', 'confirmee']))
<div class="modal fade" id="annulerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('reservations.annuler', $reservation) }}">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="bi bi-slash-circle text-warning"></i>Annuler la réservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label class="form-label">Motif de l'annulation <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('motif') is-invalid @enderror"
                                  name="motif" rows="3" required placeholder="Indiquez la raison de l'annulation…"></textarea>
                        @error('motif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-slash-circle me-1"></i>Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
