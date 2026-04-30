@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reservations.index') }}">Réservations</a></li>
            <li class="breadcrumb-item active">Réservation directe</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-lightning fs-4 text-success"></i>
        <h4 class="mb-0 fw-semibold">Réservation directe</h4>
    </div>
    <p class="text-muted mb-4" style="font-size:.875rem">
        Crée une réservation immédiatement confirmée, sans passer par le workflow de validation.
    </p>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('reservations.directe.store') }}">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label for="demandeur_id" class="form-label">
                                    <i class="bi bi-person me-1 text-muted"></i>Demandeur <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('demandeur_id') is-invalid @enderror"
                                        id="demandeur_id" name="demandeur_id" required>
                                    <option value="">Sélectionner un utilisateur…</option>
                                    @php($users = \App\Models\User::orderBy('name')->get())
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('demandeur_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('demandeur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="salle_id" class="form-label">
                                    <i class="bi bi-building me-1 text-muted"></i>Salle <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('salle_id') is-invalid @enderror"
                                        id="salle_id" name="salle_id" required>
                                    <option value="">Sélectionner une salle…</option>
                                    @foreach($salles as $salle)
                                    <option value="{{ $salle->id }}" {{ old('salle_id') == $salle->id ? 'selected' : '' }}>
                                        {{ $salle->nom }} — {{ $salle->capacite }} pers.
                                    </option>
                                    @endforeach
                                </select>
                                @error('salle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label for="date_debut" class="form-label">
                                    <i class="bi bi-calendar-event me-1 text-muted"></i>Début <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('date_debut') is-invalid @enderror"
                                       id="date_debut" name="date_debut"
                                       value="{{ old('date_debut') }}" required>
                                @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="date_fin" class="form-label">
                                    <i class="bi bi-calendar-event me-1 text-muted"></i>Fin <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('date_fin') is-invalid @enderror"
                                       id="date_fin" name="date_fin"
                                       value="{{ old('date_fin') }}" required>
                                @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Indicateur de disponibilité --}}
                        <div id="disponibiliteIndicateur" class="mb-3" style="display:none">
                            <div id="disponibiliteResult" class="d-flex align-items-center gap-2 p-2 rounded" style="font-size:.875rem"></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-chat-text me-1 text-muted"></i>Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Objet de la réunion…">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted">
                                <i class="bi bi-info-circle me-1"></i>Motif
                            </label>
                            <input type="text" class="form-control bg-light" name="motif"
                                   value="Réservation directe" readonly>
                            <small class="text-muted">Les réservations directes sont automatiquement confirmées.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-lightning me-1"></i>Créer la réservation
                            </button>
                            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const apiUrl = '{{ route("api.disponibilite") }}';
    const salleEl  = document.getElementById('salle_id');
    const debutEl  = document.getElementById('date_debut');
    const finEl    = document.getElementById('date_fin');
    const indic    = document.getElementById('disponibiliteIndicateur');
    const result   = document.getElementById('disponibiliteResult');
    let debounceTimer = null;

    function checkDispo() {
        clearTimeout(debounceTimer);
        const salle = salleEl.value;
        const debut = debutEl.value;
        const fin   = finEl.value;
        if (!salle || !debut || !fin || debut >= fin) { indic.style.display = 'none'; return; }

        result.innerHTML = '<div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>Vérification…';
        result.className = 'd-flex align-items-center gap-2 p-2 rounded bg-light text-secondary';
        result.style.background = '';
        indic.style.display = 'block';

        debounceTimer = setTimeout(function () {
            const params = new URLSearchParams({ salle_id: salle, date_debut: debut, date_fin: fin });
            fetch(apiUrl + '?' + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.disponible) {
                    result.innerHTML = '<i class="bi bi-check-circle-fill text-success fs-5"></i><span class="text-success fw-medium">Créneau disponible</span>';
                    result.style.background = 'rgba(25,135,84,.08)';
                } else {
                    result.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i><span class="text-danger fw-medium">Créneau déjà occupé — choisissez un autre horaire</span>';
                    result.style.background = 'rgba(220,53,69,.08)';
                }
                result.className = 'd-flex align-items-center gap-2 p-2 rounded';
            })
            .catch(() => { indic.style.display = 'none'; });
        }, 500);
    }

    [salleEl, debutEl, finEl].forEach(el => el.addEventListener('change', checkDispo));
    finEl.addEventListener('input', checkDispo);
    debutEl.addEventListener('input', checkDispo);
})();
</script>
@endpush
