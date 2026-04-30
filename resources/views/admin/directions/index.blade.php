@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item active">Directions</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-diagram-3 text-primary"></i>Gestion des directions
        </h4>
        <button type="button" class="btn btn-primary btn-sm"
                data-bs-toggle="modal" data-bs-target="#modalCreerDirection">
            <i class="bi bi-plus-circle me-1"></i>Nouvelle direction
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($directions->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-diagram-3 fs-1 d-block mb-3 opacity-40"></i>
                    <p class="mb-1 fw-medium">Aucune direction enregistrée.</p>
                    <button type="button" class="btn btn-primary btn-sm mt-2"
                            data-bs-toggle="modal" data-bs-target="#modalCreerDirection">
                        <i class="bi bi-plus-circle me-1"></i>Créer la première direction
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nom</th>
                                <th>Description</th>
                                <th style="width:130px">Utilisateurs</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($directions as $direction)
                            <tr>
                                <td class="ps-3 fw-semibold">
                                    <i class="bi bi-diagram-3 me-1 text-muted"></i>{{ $direction->nom }}
                                </td>
                                <td class="text-muted" style="font-size:.875rem">
                                    {{ $direction->description ?: '—' }}
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        <i class="bi bi-people me-1"></i>{{ $direction->users_count }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-action="{{ route('admin.directions.update', $direction) }}"
                                            data-nom="{{ e($direction->nom) }}"
                                            data-description="{{ e($direction->description) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditerDirection"
                                            onclick="dirEditer(this)">
                                        <i class="bi bi-pencil me-1"></i>Modifier
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-action="{{ route('admin.directions.destroy', $direction) }}"
                                            data-nom="{{ e($direction->nom) }}"
                                            data-count="{{ $direction->users_count }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalSupprimerDirection"
                                            onclick="dirSupprimer(this)">
                                        <i class="bi bi-trash me-1"></i>Supprimer
                                    </button>
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

{{-- ── Modal Créer ── --}}
<div class="modal fade" id="modalCreerDirection" tabindex="-1" aria-labelledby="modalCreerDirectionLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-1">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalCreerDirectionLabel">
                    <i class="bi bi-plus-circle text-primary"></i>Nouvelle direction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.directions.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-2">
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="bi bi-diagram-3 me-1 text-muted"></i>Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="nom"
                               placeholder="Ex : Direction des Ressources Humaines" autocomplete="off">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-medium">
                            <i class="bi bi-card-text me-1 text-muted"></i>Description
                            <span class="text-muted fw-normal">(optionnel)</span>
                        </label>
                        <textarea class="form-control" name="description" rows="2"
                                  placeholder="Courte description de la direction…"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal Éditer ── --}}
<div class="modal fade" id="modalEditerDirection" tabindex="-1" aria-labelledby="modalEditerDirectionLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-1">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalEditerDirectionLabel">
                    <i class="bi bi-pencil text-primary"></i>Modifier la direction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditerDirection" method="POST" action="" novalidate>
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body pt-2">
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="bi bi-diagram-3 me-1 text-muted"></i>Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="nom" id="editNom" autocomplete="off">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-medium">
                            <i class="bi bi-card-text me-1 text-muted"></i>Description
                            <span class="text-muted fw-normal">(optionnel)</span>
                        </label>
                        <textarea class="form-control" name="description" id="editDescription" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal Supprimer ── --}}
<div class="modal fade" id="modalSupprimerDirection" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-1">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-trash text-danger"></i>Supprimer la direction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSupprimerDirection" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-body pt-0">
                    <p class="mb-1">Voulez-vous supprimer la direction <strong id="deleteNom"></strong> ?</p>
                    <p id="deleteWarning" class="text-warning small mb-0 d-none">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <span id="deleteWarningText"></span>
                    </p>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dirEditer(btn) {
    document.getElementById('formEditerDirection').action = btn.dataset.action;
    document.getElementById('editNom').value              = btn.dataset.nom;
    document.getElementById('editDescription').value     = btn.dataset.description || '';
}

function dirSupprimer(btn) {
    document.getElementById('formSupprimerDirection').action = btn.dataset.action;
    document.getElementById('deleteNom').textContent         = btn.dataset.nom;
    var count = parseInt(btn.dataset.count, 10);
    var warn  = document.getElementById('deleteWarning');
    if (count > 0) {
        document.getElementById('deleteWarningText').textContent =
            count + ' utilisateur' + (count > 1 ? 's seront' : ' sera') +
            ' désaffecté' + (count > 1 ? 's' : '') + '.';
        warn.classList.remove('d-none');
    } else {
        warn.classList.add('d-none');
    }
}
</script>
@endpush
