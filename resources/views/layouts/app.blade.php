<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gestion Salles') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @stack('styles')
</head>
<body>

{{-- ── Toast container ── --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999">
    @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0" id="toastSuccess" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="toast align-items-center text-bg-danger border-0" id="toastError" data-bs-delay="7000">
        <div class="d-flex">
            <div class="toast-body"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
    @if($errors->any())
    <div class="toast align-items-center text-bg-danger border-0" id="toastErrors" data-bs-autohide="false">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0 ps-3 mt-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto align-self-start mt-2" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>

@auth
{{-- ── Barre mobile ── --}}
<nav class="navbar navbar-dark d-md-none px-3 py-2" style="background:#1565c0;border-bottom:1px solid rgba(255,255,255,.1)">
    <button class="btn btn-link text-white p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="navbar-brand mb-0 fs-6 fw-semibold">
        <i class="bi bi-building me-1 text-primary"></i>Gestion Salles
    </span>
</nav>

{{-- ── Sidebar mobile (offcanvas) ── --}}
<div class="offcanvas offcanvas-start text-white" id="sidebarMobile" style="width:260px;background:#1565c0 !important">
    <div class="offcanvas-header sidebar-user">
        <div>
            <div class="fw-semibold text-white">{{ Auth::user()->name }}</div>
            @if(Auth::user()->hasRole('admin'))
            <span class="badge bg-danger mt-1">Admin</span>
            @elseif(Auth::user()->hasRole('responsable'))
            <span class="badge bg-warning text-dark mt-1">Responsable</span>
            @else
            <span class="badge bg-secondary mt-1">Utilisateur</span>
            @endif
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0 overflow-auto">
        @include('layouts.sidebar-nav')
    </div>
</div>

{{-- ── Modal Nouvelle demande de réservation ── --}}
@php($sallesModal = \App\Models\Salle::where('active', true)->orderBy('nom')->get())
<div class="modal fade" id="modalNouvelleReservation" tabindex="-1" aria-labelledby="modalNouvelleReservationLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-1">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNouvelleReservationLabel">
                    <i class="bi bi-plus-circle text-primary"></i>Nouvelle demande de réservation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNouvelleReservation" action="{{ route('reservations.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body pt-2">
                    <div id="errorsNouvelleReservation" class="alert alert-danger d-none mb-3"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bi bi-building me-1 text-muted"></i>Salle <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="salle_id" id="modalSalleId" required>
                                <option value="">Sélectionner une salle…</option>
                                @foreach($sallesModal as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->nom }} — {{ $s->capacite }} pers.{{ $s->equipement ? ' · ' . Str::limit($s->equipement, 30) : '' }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">
                                <i class="bi bi-calendar-event me-1 text-muted"></i>Début <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control" name="date_debut" id="modalDateDebut" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">
                                <i class="bi bi-calendar-event me-1 text-muted"></i>Fin <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control" name="date_fin" id="modalDateFin" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12" id="modalDispoRow" style="display:none">
                            <div id="modalDispoResult" class="d-flex align-items-center gap-2 p-2 rounded" style="font-size:.875rem"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bi bi-chat-text me-1 text-muted"></i>Description
                                <span class="text-muted fw-normal">(optionnel)</span>
                            </label>
                            <textarea class="form-control" name="description" rows="2"
                                      placeholder="Objet de la réunion, nombre de participants…"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="btnSoumettreDemande">
                        <i class="bi bi-send me-1"></i>Soumettre la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<div class="container-fluid">
    <div class="row">
        @auth
        <nav class="col-md-2 d-none d-md-block sidebar py-3">
            <div class="sidebar-user text-center mb-1">
                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-2"
                     style="width:42px;height:42px">
                    <i class="bi bi-person-fill text-white fs-5"></i>
                </div>
                <div class="text-white fw-semibold" style="font-size:.875rem">{{ Auth::user()->name }}</div>
                @if(Auth::user()->hasRole('admin'))
                <span class="badge bg-danger mt-1">Admin</span>
                @elseif(Auth::user()->hasRole('responsable'))
                <span class="badge bg-warning text-dark mt-1">Responsable</span>
                @else
                <span class="badge bg-secondary mt-1">Utilisateur</span>
                @endif
            </div>
            @include('layouts.sidebar-nav')
        </nav>
        @endauth

        <main class="@auth col-md-10 ms-auto @else col-12 @endif py-4">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ── Helpers globaux ──
function showDynToast(type, message) {
    const container = document.querySelector('.toast-container');
    const bgClass = type === 'success' ? 'text-bg-success' : 'text-bg-danger';
    const icon    = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    const el = document.createElement('div');
    el.className = `toast align-items-center ${bgClass} border-0`;
    el.innerHTML = `<div class="d-flex"><div class="toast-body"><i class="bi ${icon} me-2"></i>${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    container.appendChild(el);
    new bootstrap.Toast(el, { delay: 5000 }).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

function clearFormErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => { el.textContent = ''; });
}

function applyFormErrors(form, errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.textContent = messages[0];
        }
    });
}

async function submitModalForm({ form, errBox, btn, btnOriginalHtml, onSuccess }) {
    clearFormErrors(form);
    errBox.classList.add('d-none');
    errBox.innerHTML = '';
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi…';

    try {
        const resp = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        });

        if (resp.ok) {
            const data = await resp.json();
            onSuccess(data);
        } else if (resp.status === 422) {
            const data = await resp.json();
            applyFormErrors(form, data.errors || {});
            const genError = data.errors?.reservation || data.errors?.general;
            if (genError) {
                errBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${genError[0]}`;
                errBox.classList.remove('d-none');
            } else if (!Object.keys(data.errors || {}).length) {
                errBox.innerHTML = data.message || 'Veuillez corriger les erreurs.';
                errBox.classList.remove('d-none');
            }
        } else {
            errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Une erreur est survenue. Veuillez réessayer.';
            errBox.classList.remove('d-none');
        }
    } catch {
        errBox.innerHTML = '<i class="bi bi-wifi-off me-2"></i>Erreur réseau. Veuillez réessayer.';
        errBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerHTML = btnOriginalHtml;
    }
}

window.addEventListener('load', function () {
    // Flash depuis sessionStorage (après reload post-modal)
    const flash = sessionStorage.getItem('flashSuccess');
    if (flash) { sessionStorage.removeItem('flashSuccess'); showDynToast('success', flash); }

    // Init toasts serveur
    ['toastSuccess','toastError','toastErrors'].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        new bootstrap.Toast(el, { autohide: el.dataset.bsAutohide !== 'false' }).show();
    });

    // ── Modal Nouvelle demande ──
    const formRes  = document.getElementById('formNouvelleReservation');
    const modalEl  = document.getElementById('modalNouvelleReservation');
    if (formRes && modalEl) {
        const errBox  = document.getElementById('errorsNouvelleReservation');
        const btn     = document.getElementById('btnSoumettreDemande');
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const apiUrl  = '{{ route("api.disponibilite") }}';
        let debounce  = null;

        modalEl.addEventListener('hidden.bs.modal', function () {
            formRes.reset();
            clearFormErrors(formRes);
            errBox.classList.add('d-none');
            errBox.innerHTML = '';
            const row = document.getElementById('modalDispoRow');
            if (row) row.style.display = 'none';
        });

        function checkDispo() {
            clearTimeout(debounce);
            const salle = document.getElementById('modalSalleId').value;
            const debut = document.getElementById('modalDateDebut').value;
            const fin   = document.getElementById('modalDateFin').value;
            const row   = document.getElementById('modalDispoRow');
            const res   = document.getElementById('modalDispoResult');
            if (!salle || !debut || !fin || debut >= fin) { row.style.display = 'none'; return; }
            res.innerHTML = '<div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>Vérification…';
            res.className = 'd-flex align-items-center gap-2 p-2 rounded bg-light text-secondary';
            res.style.background = '';
            row.style.display = 'block';
            debounce = setTimeout(function () {
                fetch(apiUrl + '?' + new URLSearchParams({ salle_id: salle, date_debut: debut, date_fin: fin }), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(d => {
                    res.className = 'd-flex align-items-center gap-2 p-2 rounded';
                    if (d.disponible) {
                        res.innerHTML = '<i class="bi bi-check-circle-fill text-success fs-5"></i><span class="text-success fw-medium">Créneau disponible</span>';
                        res.style.background = 'rgba(25,135,84,.08)';
                    } else {
                        res.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i><span class="text-danger fw-medium">Créneau déjà occupé</span>';
                        res.style.background = 'rgba(220,53,69,.08)';
                    }
                })
                .catch(() => { row.style.display = 'none'; });
            }, 500);
        }
        ['modalSalleId','modalDateDebut','modalDateFin'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.addEventListener('change', checkDispo); el.addEventListener('input', checkDispo); }
        });

        formRes.addEventListener('submit', function (e) {
            e.preventDefault();
            submitModalForm({
                form: formRes,
                errBox,
                btn,
                btnOriginalHtml: '<i class="bi bi-send me-1"></i>Soumettre la demande',
                onSuccess(data) {
                    bsModal.hide();
                    sessionStorage.setItem('flashSuccess', data.message || 'Demande soumise avec succès.');
                    location.reload();
                }
            });
        });
    }
});
</script>
@stack('scripts')
</body>
</html>
