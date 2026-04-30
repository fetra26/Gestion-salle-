@extends('layouts.app')

@push('styles')
<style>
    /* Barre d'outils FullCalendar */
    .fc .fc-toolbar.fc-header-toolbar { margin-bottom: 1rem; flex-wrap: wrap; gap: .5rem; }
    .fc .fc-button { font-size: .85rem; padding: .35rem .75rem; }
    .fc .fc-button-primary { background-color: #0d6efd; border-color: #0d6efd; }
    .fc .fc-button-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active { background-color: #0a58ca; border-color: #0a53be; }
    .fc .fc-today-button { background-color: #6c757d; border-color: #6c757d; }
    .fc .fc-today-button:hover { background-color: #5c636a; }

    /* Cellule du jour courant */
    .fc .fc-day-today { background-color: #e8f0fe !important; }
    .fc .fc-timegrid-col.fc-day-today { background-color: #e8f0fe !important; }

    /* Événements */
    .fc-event { border-radius: 4px !important; font-size: .8rem; padding: 1px 4px; border: none !important; }
    .fc-event-title { font-weight: 600; }
    .fc-event-time { font-weight: 400; opacity: .9; }

    /* Heures */
    .fc .fc-timegrid-slot-label { font-size: .8rem; color: #6c757d; }
    .fc .fc-col-header-cell { background: #f8f9fa; font-size: .85rem; }
    .fc .fc-col-header-cell-cushion { padding: .5rem; color: #333; text-decoration: none; }
    .fc-col-header-cell.fc-day-today .fc-col-header-cell-cushion { color: #0d6efd; font-weight: 700; }

    /* Indicateur "maintenant" */
    .fc .fc-timegrid-now-indicator-line { border-color: #dc3545; border-width: 2px; }
    .fc .fc-timegrid-now-indicator-arrow { border-top-color: #dc3545; border-bottom-color: #dc3545; }

    /* Heures hors plage bureau */
    .fc .fc-non-business { background: rgba(0,0,0,.03); }

    /* Légende */
    .legende-item { display: flex; align-items: center; gap: 6px; font-size: .85rem; }
    .legende-dot { width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0; }

    /* Modal statut badge */
    .statut-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3">

    {{-- Barre supérieure --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="mb-0 fw-semibold">Calendrier des réservations</h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Filtre salle --}}
            <select class="form-select form-select-sm" id="filtreSalle" style="width:200px;">
                <option value="">Toutes les salles</option>
                @foreach($salles as $salle)
                <option value="{{ $salle->id }}" {{ $salleId == $salle->id ? 'selected' : '' }}>
                    {{ $salle->nom }}
                </option>
                @endforeach
            </select>

            {{-- Filtre statut --}}
            <select class="form-select form-select-sm" id="filtreStatut" style="width:160px;">
                <option value="">Tous les statuts</option>
                <option value="confirmee">Confirmées</option>
                <option value="en_attente">En attente</option>
                <option value="refusee">Refusées</option>
                <option value="annulee">Annulées</option>
            </select>

            <button type="button" class="btn btn-primary btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalNouvelleReservation">
                <i class="bi bi-plus-circle me-1"></i>Nouvelle demande
            </button>
        </div>
    </div>

    {{-- Légende --}}
    <div class="d-flex gap-3 mb-3 flex-wrap">
        <div class="legende-item"><div class="legende-dot" style="background:#198754"></div> Confirmée</div>
        <div class="legende-item"><div class="legende-dot" style="background:#fd7e14"></div> En attente</div>
        <div class="legende-item"><div class="legende-dot" style="background:#dc3545"></div> Refusée</div>
        <div class="legende-item"><div class="legende-dot" style="background:#adb5bd"></div> Annulée</div>
    </div>

    {{-- Calendrier --}}
    <div class="card shadow-sm">
        <div class="card-body p-2">
            <div id="calendar"></div>
        </div>
    </div>
</div>

{{-- Modal détail réservation --}}
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <div>
                    <h5 class="modal-title mb-0" id="reservationModalLabel" id="modalTitre"></h5>
                    <small class="text-muted" id="modalSalle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span id="modalStatutBadge" class="statut-badge"></span>
                </div>
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th class="text-muted fw-normal ps-0" style="width:110px">Début</th>
                            <td id="modalDebut" class="fw-semibold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal ps-0">Fin</th>
                            <td id="modalFin" class="fw-semibold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal ps-0">Durée</th>
                            <td id="modalDuree"></td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal ps-0">Demandeur</th>
                            <td id="modalDemandeur"></td>
                        </tr>
                        <tr id="rowDescription" style="display:none">
                            <th class="text-muted fw-normal ps-0">Description</th>
                            <td id="modalDescription"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
                <a href="#" id="modalLien" class="btn btn-primary btn-sm">Voir la fiche</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const apiUrl   = '{{ route("api.calendrier.reservations") }}';
    const calEl    = document.getElementById('calendar');

    const statutLabels = {
        confirmee:  { label: 'Confirmée',  color: '#198754', bg: '#d1e7dd' },
        en_attente: { label: 'En attente', color: '#fd7e14', bg: '#fff3cd' },
        refusee:    { label: 'Refusée',    color: '#dc3545', bg: '#f8d7da' },
        annulee:    { label: 'Annulée',    color: '#6c757d', bg: '#e9ecef' },
        terminee:   { label: 'Terminée',   color: '#0dcaf0', bg: '#cff4fc' },
    };

    const calendar = new FullCalendar.Calendar(calEl, {
        locale: 'fr',
        initialView: 'timeGridWeek',

        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
        },

        buttonText: {
            today:    "Aujourd'hui",
            month:    'Mois',
            week:     'Semaine',
            day:      'Jour',
            list:     'Liste',
        },

        views: {
            timeGridWeek: { titleFormat: { year: 'numeric', month: 'long', day: 'numeric' } },
            timeGridDay:  { titleFormat: { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' } },
        },

        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '00:30:00',
        slotLabelInterval: '01:00',
        slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

        allDaySlot: false,
        nowIndicator: true,
        weekNumbers: false,
        navLinks: true,

        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '08:00',
            endTime: '18:00',
        },

        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

        height: 'auto',
        expandRows: true,

        // Source des événements
        events: function (fetchInfo, successCallback, failureCallback) {
            const params = new URLSearchParams();
            const salle  = document.getElementById('filtreSalle').value;
            const statut = document.getElementById('filtreStatut').value;
            if (salle)  params.set('salle_id', salle);
            if (statut) params.set('statut', statut);

            fetch(apiUrl + (params.toString() ? '?' + params : ''), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(successCallback)
            .catch(err => { console.error('Calendrier :', err); failureCallback(err); });
        },

        // Personnalisation du rendu des événements
        eventDidMount: function (info) {
            const statut = info.event.extendedProps.statut;
            const meta   = statutLabels[statut] || {};
            info.el.style.backgroundColor = meta.color || '#6c757d';
            info.el.style.borderLeft = '3px solid rgba(0,0,0,.2)';
            info.el.style.color = '#fff';
            info.el.title = info.event.title;
            info.el.querySelectorAll('.fc-event-title,.fc-event-time,.fc-list-event-title').forEach(function (el) {
                el.style.color = '#fff';
            });
        },

        // Clic sur un événement → modal
        eventClick: function (info) {
            const event = info.event;
            const props = event.extendedProps;
            const meta  = statutLabels[props.statut] || { label: props.statut, color: '#333', bg: '#eee' };

            // En-tête coloré
            document.getElementById('modalHeader').style.borderBottom = '3px solid ' + meta.color;
            document.getElementById('reservationModalLabel').textContent = props.salle;
            document.getElementById('modalSalle').textContent = event.title;

            // Badge statut
            const badge = document.getElementById('modalStatutBadge');
            badge.textContent = meta.label;
            badge.style.backgroundColor = meta.bg;
            badge.style.color = meta.color;

            // Dates
            const fmt = d => d ? d.toLocaleString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
            document.getElementById('modalDebut').textContent = fmt(event.start);
            document.getElementById('modalFin').textContent   = fmt(event.end);

            // Durée
            if (event.start && event.end) {
                const diff = Math.round((event.end - event.start) / 60000);
                const h = Math.floor(diff / 60), m = diff % 60;
                document.getElementById('modalDuree').textContent = h > 0 ? h + 'h' + (m > 0 ? m + 'min' : '') : m + ' min';
            }

            document.getElementById('modalDemandeur').textContent = props.demandeur;

            // Description
            if (props.description) {
                document.getElementById('modalDescription').textContent = props.description;
                document.getElementById('rowDescription').style.display = '';
            } else {
                document.getElementById('rowDescription').style.display = 'none';
            }

            document.getElementById('modalLien').href = '/reservations/' + event.id;

            new bootstrap.Modal(document.getElementById('reservationModal')).show();
        },
    });

    calendar.render();

    // Filtres
    ['filtreSalle', 'filtreStatut'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => calendar.refetchEvents());
    });
});
</script>
@endpush
