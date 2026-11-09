@extends('layouts.principal')
@section('titre', 'Rapports')
@section('contenu')

{{-- ===== Choix de la période ===== --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Du</label>
                <input type="date" name="du" class="form-control" value="{{ $du->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Au</label>
                <input type="date" name="au" class="form-control" value="{{ $au->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Afficher</button>
            </div>
            <div class="col-md-4 d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="{{ route('rapports', ['du' => now()->startOfMonth()->format('Y-m-d'), 'au' => now()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-secondary">Ce mois</a>
                <a href="{{ route('rapports', ['du' => now()->startOfYear()->format('Y-m-d'), 'au' => now()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-secondary">Cette année</a>
                <a href="{{ route('rapports') }}" class="btn btn-sm btn-outline-secondary">6 derniers mois</a>
            </div>
        </form>
    </div>
</div>

{{-- ===== Chiffres clés ===== --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-clipboard2-data"></i></div>
            <div>
                <div class="text-muted small">Interventions sur la période</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['total'] }}</div>
                <div class="small text-muted mt-1">{{ $stats['actives'] }} encore active(s)</div>
            </div>
        </div></div>
    </div>
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check-lg"></i></div>
            <div>
                <div class="text-muted small">Taux de résolution</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['taux'] }} %</div>
                <div class="small text-muted mt-1">{{ $stats['termine'] }} terminée(s)</div>
            </div>
        </div></div>
    </div>
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-stopwatch"></i></div>
            <div>
                <div class="text-muted small">Durée moyenne</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['duree_moyenne'] }}</div>
                <div class="small text-muted mt-1">entre début et fin</div>
            </div>
        </div></div>
    </div>
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-x-lg"></i></div>
            <div>
                <div class="text-muted small">Annulées</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['annule'] }}</div>
                <div class="small text-muted mt-1">sur la période</div>
            </div>
        </div></div>
    </div>
</div>

{{-- ===== Graphiques ===== --}}
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-graph-up me-2 text-primary"></i>Interventions par mois</div>
            <div class="card-body"><div style="height:300px"><canvas id="graphMois"></canvas></div></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Par type</div>
            <div class="card-body">
                @if (count($parType['data']))
                    <div style="height:300px"><canvas id="graphType"></canvas></div>
                @else
                    <div class="text-center text-muted py-5">Aucune donnée.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-person-gear me-2 text-primary"></i>Interventions par technicien</div>
            <div class="card-body">
                @if (count($parTechnicien['labels']))
                    <div style="height:300px"><canvas id="graphTechnicien"></canvas></div>
                @else
                    <div class="text-center text-muted py-5">Aucun technicien.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-pc-display me-2 text-primary"></i>Équipements les plus concernés</div>
            <ul class="list-group list-group-flush">
                @forelse ($topEquipements as $e)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                       <span>{{ $e->nom }} <small class="text-muted d-block">{{ $e->reference ?? $e->type }}</small></span>
                        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">{{ $e->n }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-3">Aucune donnée.</li>
                @endforelse
            </ul>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-people me-2 text-primary"></i>Clients les plus actifs</div>
            <ul class="list-group list-group-flush">
                @forelse ($topClients as $c)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $c->nom }}</span>
                        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">{{ $c->n }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-3">Aucune donnée.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    const bleu = '#2563eb', vert = '#16a34a';

    // Par mois
    new Chart(document.getElementById('graphMois'), {
        type: 'bar',
        data: {
            labels: @json($parMois['labels']),
            datasets: [
                { label: 'Créées',    data: @json($parMois['creees']),    backgroundColor: bleu, borderRadius: 6 },
                { label: 'Terminées', data: @json($parMois['terminees']), backgroundColor: vert, borderRadius: 6 },
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Par type
    @if (count($parType['data']))
    new Chart(document.getElementById('graphType'), {
        type: 'doughnut',
        data: {
            labels: @json($parType['labels']),
            datasets: [{ data: @json($parType['data']), backgroundColor: ['#2563eb', '#16a34a', '#f59e0b', '#8b5cf6', '#ef4444'] }]
        },
        options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
    });
    @endif

    // Par technicien
    @if (count($parTechnicien['labels']))
    new Chart(document.getElementById('graphTechnicien'), {
        type: 'bar',
        data: {
            labels: @json($parTechnicien['labels']),
            datasets: [
                { label: 'Total',     data: @json($parTechnicien['total']),     backgroundColor: bleu, borderRadius: 6 },
                { label: 'Terminées', data: @json($parTechnicien['terminees']), backgroundColor: vert, borderRadius: 6 },
            ]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { position: 'bottom' } }
        }
    });
    @endif
</script>
@endsection