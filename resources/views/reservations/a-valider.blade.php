@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item active">À valider</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-check2-circle fs-4 text-warning"></i>
        <h4 class="mb-0 fw-semibold">Demandes à valider</h4>
        @if(!$reservations->isEmpty())
        <span class="badge bg-danger">{{ $reservations->total() }}</span>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($reservations->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-all fs-1 d-block mb-3 text-success opacity-75"></i>
                    <p class="mb-0 fw-medium">Tout est traité !</p>
                    <small>Aucune demande en attente de validation.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Salle</th>
                                <th>Demandeur</th>
                                <th>Créneau</th>
                                <th>Description</th>
                                <th class="text-end pe-3">Actions rapides</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                            <tr>
                                <td class="ps-3">
                                    <i class="bi bi-building me-1 text-muted"></i>
                                    <strong>{{ $reservation->salle->nom }}</strong>
                                </td>
                                <td>
                                    <i class="bi bi-person me-1 text-muted"></i>{{ $reservation->demandeur->name }}
                                </td>
                                <td>
                                    <div style="font-size:.85rem">
                                        {{ $reservation->date_debut->format('d/m/Y H:i') }}
                                    </div>
                                    <small class="text-muted">
                                        → {{ $reservation->date_fin->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size:.85rem">
                                        {{ Str::limit($reservation->description, 50) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('reservations.show', $reservation) }}"
                                       class="btn btn-sm btn-outline-secondary me-1">
                                        <i class="bi bi-eye me-1"></i>Voir
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-success me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#validerModal"
                                            data-url="{{ route('reservations.valider', $reservation) }}"
                                            data-salle="{{ $reservation->salle->nom }}"
                                            data-demandeur="{{ $reservation->demandeur->name }}">
                                        <i class="bi bi-check-lg me-1"></i>Valider
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#refuserModal"
                                            data-url="{{ route('reservations.refuser', $reservation) }}"
                                            data-salle="{{ $reservation->salle->nom }}"
                                            data-demandeur="{{ $reservation->demandeur->name }}">
                                        <i class="bi bi-x-lg me-1"></i>Refuser
                                    </button>
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

{{-- Modal Valider (partagé) --}}
<div class="modal fade" id="validerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formValider">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle text-success"></i>Valider la demande
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="text-muted mb-3" id="validerInfo"></p>
                    <div class="mb-3">
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" name="motif" rows="2" placeholder="Message pour le demandeur…"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Refuser (partagé) --}}
<div class="modal fade" id="refuserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formRefuser">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="bi bi-x-circle text-danger"></i>Refuser la demande
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="text-muted mb-3" id="refuserInfo"></p>
                    <div class="mb-3">
                        <label class="form-label">Motif du refus <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motif" rows="3" required
                                  placeholder="Indiquez la raison du refus…"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Refuser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('validerModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('formValider').action = btn.dataset.url;
        document.getElementById('validerInfo').textContent =
            'Salle : ' + btn.dataset.salle + ' — Demandeur : ' + btn.dataset.demandeur;
    });

    document.getElementById('refuserModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('formRefuser').action = btn.dataset.url;
        document.getElementById('refuserInfo').textContent =
            'Salle : ' + btn.dataset.salle + ' — Demandeur : ' + btn.dataset.demandeur;
        // reset textarea
        document.querySelector('#formRefuser textarea[name="motif"]').value = '';
    });
});
</script>
@endpush
