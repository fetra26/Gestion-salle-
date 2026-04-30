@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center mb-4 gap-2">
        <i class="bi bi-speedometer2 fs-4 text-primary"></i>
        <h4 class="mb-0 fw-semibold">Tableau de bord</h4>
    </div>

    {{-- Statistiques principales --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #0d6efd !important">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:rgba(13,110,253,.12)">
                        <i class="bi bi-calendar2-check fs-5 text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.78rem">Total réservations</div>
                        <div class="fw-bold fs-4 lh-1">{{ $statistiques['total_reservations'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #fd7e14 !important">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:rgba(253,126,20,.12)">
                        <i class="bi bi-hourglass-split fs-5 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.78rem">En attente</div>
                        <div class="fw-bold fs-4 lh-1">{{ $statistiques['reservations_en_attente'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #198754 !important">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:rgba(25,135,84,.12)">
                        <i class="bi bi-patch-check fs-5 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.78rem">Confirmées</div>
                        <div class="fw-bold fs-4 lh-1">{{ $statistiques['reservations_confirmees'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #0dcaf0 !important">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:rgba(13,202,240,.12)">
                        <i class="bi bi-calendar-day fs-5 text-info"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.78rem">Aujourd'hui</div>
                        <div class="fw-bold fs-4 lh-1">{{ $statistiques['reservations_aujourdhui'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques secondaires --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-2">
                <div class="card-body py-2">
                    <i class="bi bi-calendar-week text-primary mb-1 d-block fs-5"></i>
                    <div class="text-muted" style="font-size:.78rem">Cette semaine</div>
                    <div class="fw-bold fs-5 text-primary">{{ $statistiques['reservations_semaine'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-2">
                <div class="card-body py-2">
                    <i class="bi bi-graph-up-arrow text-success mb-1 d-block fs-5"></i>
                    <div class="text-muted" style="font-size:.78rem">Taux de validation</div>
                    <div class="fw-bold fs-5 text-success">{{ $statistiques['taux_validation'] }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart text-primary"></i>
                    <span class="fw-medium">Réservations par salle</span>
                </div>
                <div class="card-body">
                    <canvas id="chartParSalle" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart text-primary"></i>
                    <span class="fw-medium">Réservations par mois</span>
                </div>
                <div class="card-body">
                    <canvas id="chartParMois" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Mes prochaines réservations --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-medium d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary"></i> Mes prochaines réservations
                    </span>
                    <a href="{{ route('reservations.mes-reservations') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($mesReservations->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                            <small>Aucune réservation à venir.</small>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#modalNouvelleReservation">
                                    <i class="bi bi-plus-circle me-1"></i>Faire une demande
                                </button>
                            </div>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($mesReservations as $r)
                            <li class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.875rem">
                                            <i class="bi bi-building me-1 text-muted"></i>{{ $r->salle->nom }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($r->date_debut)->format('d/m/Y H:i') }}
                                            → {{ \Carbon\Carbon::parse($r->date_fin)->format('H:i') }}
                                        </small>
                                    </div>
                                    <x-statut-badge :statut="$r->statut" />
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Demandes à valider (responsable/admin) --}}
        @can('peutValider')
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-medium d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle text-warning"></i> Demandes à valider
                        @if(!empty($reservationsAValider) && count($reservationsAValider) > 0)
                        <span class="badge bg-danger">{{ count($reservationsAValider) }}</span>
                        @endif
                    </span>
                    <a href="{{ route('reservations.a-valider') }}" class="btn btn-sm btn-outline-warning">
                        Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(empty($reservationsAValider) || count($reservationsAValider) === 0)
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-all fs-2 d-block mb-2 text-success opacity-75"></i>
                            <small>Aucune demande en attente.</small>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($reservationsAValider as $r)
                            <li class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.875rem">{{ $r->salle->nom }}</div>
                                        <small class="text-muted">
                                            {{ $r->demandeur->name }} —
                                            {{ \Carbon\Carbon::parse($r->date_debut)->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('reservations.show', $r) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-arrow-right-circle me-1"></i>Traiter
                                    </a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const parSalleLabels = {!! json_encode(array_column($statistiques['reservations_par_salle'], 'salle')) !!};
    const parSalleData   = {!! json_encode(array_column($statistiques['reservations_par_salle'], 'count')) !!};

    new Chart(document.getElementById('chartParSalle'), {
        type: 'doughnut',
        data: {
            labels: parSalleLabels.length ? parSalleLabels : ['Aucune donnée'],
            datasets: [{
                data: parSalleData.length ? parSalleData : [1],
                backgroundColor: ['#0d6efd','#198754','#fd7e14','#dc3545','#0dcaf0','#6f42c1'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 14, font: { size: 12 } } }
            },
            cutout: '62%',
        }
    });

    new Chart(document.getElementById('chartParMois'), {
        type: 'bar',
        data: {
            labels: ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
            datasets: [{
                label: 'Réservations',
                data: {!! json_encode(array_values($statistiques['reservations_par_mois'])) !!},
                backgroundColor: 'rgba(13,110,253,.7)',
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
