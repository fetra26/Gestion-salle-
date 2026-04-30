@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item active">Utilisateurs</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-people text-primary"></i>Gestion des utilisateurs
        </h4>
        <button type="button" class="btn btn-primary btn-sm"
                data-bs-toggle="modal" data-bs-target="#modalNouvelUser">
            <i class="bi bi-person-plus me-1"></i>Nouvel utilisateur
        </button>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label form-label-sm text-muted">Recherche</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Nom ou email…" value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm text-muted">Rôle</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label form-label-sm text-muted">Direction</label>
                    <select name="direction_id" class="form-select form-select-sm">
                        <option value="">Toutes les directions</option>
                        @foreach($directions as $direction)
                        <option value="{{ $direction->id }}" {{ request('direction_id') == $direction->id ? 'selected' : '' }}>
                            {{ $direction->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Réinit.
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-3 opacity-40"></i>
                    <p class="mb-1">Aucun utilisateur trouvé.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nom</th>
                                <th>Email</th>
                                <th>Direction</th>
                                <th>Rôle</th>
                                <th>Inscrit le</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="ps-3">
                                    <i class="bi bi-person me-1 text-muted"></i>{{ $user->name }}
                                </td>
                                <td><span class="text-muted">{{ $user->email }}</span></td>
                                <td>
                                    @if($user->direction)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-diagram-3 me-1"></i>{{ $user->direction->nom }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($user->roles as $role)
                                    <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'responsable' ? 'warning text-dark' : 'primary') }}">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                    @endforeach
                                </td>
                                <td class="text-muted" style="font-size:.875rem">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>Modifier
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer {{ addslashes($user->name) }} ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Supprimer
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                <div class="px-3 py-2">{{ $users->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- ── Modal Nouvel utilisateur ── --}}
<div class="modal fade" id="modalNouvelUser" tabindex="-1" aria-labelledby="modalNouvelUserLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNouvelUserLabel">
                    <i class="bi bi-person-plus text-primary"></i>Nouvel utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNouvelUser" action="{{ route('admin.users.store') }}" method="POST" novalidate>
                @csrf
                <div id="errorsNouvelUser" class="alert alert-danger d-none mx-3 mt-3 mb-0"></div>

                {{-- Informations personnelles --}}
                <div class="px-3 pt-3 pb-1">
                    <p class="text-muted fw-semibold mb-2" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em">
                        <i class="bi bi-person me-1"></i>Informations personnelles
                    </p>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" autocomplete="off" placeholder="Jean Dupont">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium">Adresse email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" autocomplete="off" placeholder="jean.dupont@exemple.com">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <hr class="mx-3 my-2">

                {{-- Affectation --}}
                <div class="px-3 pb-1">
                    <p class="text-muted fw-semibold mb-2" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em">
                        <i class="bi bi-diagram-3 me-1"></i>Affectation
                    </p>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium">Rôle <span class="text-danger">*</span></label>
                            <select class="form-select" name="role">
                                <option value="">Sélectionner…</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium">Direction <span class="text-danger">*</span></label>
                            <select class="form-select" name="direction_id">
                                <option value="">Sélectionner…</option>
                                @foreach($directions as $direction)
                                <option value="{{ $direction->id }}">{{ $direction->nom }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <hr class="mx-3 my-2">

                {{-- Sécurité --}}
                <div class="px-3 pb-3">
                    <p class="text-muted fw-semibold mb-2" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em">
                        <i class="bi bi-lock me-1"></i>Sécurité
                    </p>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" autocomplete="new-password">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium">Confirmer <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnCreerUser">
                        <i class="bi bi-person-plus me-1"></i>Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {
    const form    = document.getElementById('formNouvelUser');
    const modalEl = document.getElementById('modalNouvelUser');
    const errBox  = document.getElementById('errorsNouvelUser');
    const btn     = document.getElementById('btnCreerUser');

    modalEl.addEventListener('hidden.bs.modal', function () {
        form.reset();
        clearFormErrors(form);
        errBox.classList.add('d-none');
        errBox.innerHTML = '';
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitModalForm({
            form,
            errBox,
            btn,
            btnOriginalHtml: '<i class="bi bi-person-plus me-1"></i>Créer l\'utilisateur',
            onSuccess(data) {
                bootstrap.Modal.getInstance(modalEl).hide();
                sessionStorage.setItem('flashSuccess', data.message || 'Utilisateur créé avec succès.');
                location.reload();
            }
        });
    });
});
</script>
@endpush
