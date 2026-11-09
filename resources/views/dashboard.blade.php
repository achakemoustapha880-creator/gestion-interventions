@extends('layouts.principal')
@section('titre', 'Tableau de bord')
@section('contenu')

<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Bonjour, {{ auth()->user()->name }} 👋</h1>
    <div class="text-muted">Voici un aperçu de vos interventions</div>
</div>

{{-- ===== Cartes de statistiques ===== --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3 mb-4">
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-clipboard2-data"></i></div>
            <div>
                <div class="text-muted small">Total</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['total'] }}</div>
                <div class="small text-success mt-1"><i class="bi bi-arrow-up"></i> +{{ $stats['cette_semaine'] }} cette semaine</div>
            </div>
        </div></div>
    </div>
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check-lg"></i></div>
            <div>
                <div class="text-muted small">Terminées</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['termine'] }}</div>
                <div class="small text-muted mt-1">{{ $stats['pct_termine'] }} % du total</div>
            </div>
        </div></div>
    </div>
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="text-muted small">En cours</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['en_cours'] }}</div>
                <div class="small text-muted mt-1">{{ $stats['pct_en_cours'] }} % du total</div>
            </div>
        </div></div>
    </div>
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-clock"></i></div>
            <div>
                <div class="text-muted small">En attente</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['en_attente'] }}</div>
                <div class="small text-muted mt-1">{{ $stats['pct_en_attente'] }} % du total</div>
            </div>
        </div></div>
    </div>
    <div class="col">
        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-x-lg"></i></div>
            <div>
                <div class="text-muted small">Annulées</div>
                <div class="fs-2 fw-bold lh-1">{{ $stats['annule'] }}</div>
                <div class="small text-muted mt-1">{{ $stats['pct_annule'] }} % du total</div>
            </div>
        </div></div>
    </div>
</div>

@if ($stats['sans_technicien'] > 0)
    <div class="alert alert-warning d-flex align-items-center mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ $stats['sans_technicien'] }} intervention(s) active(s) sans technicien assigné.
        <a href="{{ route('interventions.index') }}" class="ms-auto fw-semibold text-dark">Voir</a>
    </div>
@endif

<div class="row g-4">
    {{-- ===== Dernières interventions ===== --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header bg-white d-flex align-items-center">
                <div class="fw-semibold"><i class="bi bi-list-ul me-2 text-primary"></i>Dernières interventions</div>
                <a href="{{ route('interventions.create') }}" class="btn btn-primary btn-sm ms-auto">
                    <i class="bi bi-plus-lg me-1"></i> Nouvelle intervention
                </a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>N°</th><th>Client</th><th>Équipement</th><th>Problème</th><th>Date</th><th>Statut</th><th>Technicien</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($recentes as $i)
                        @php $c = $i->statut->couleur(); @endphp
                        <tr>
                           <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-medium">{{ $i->client->nom }}</td>
                            <td>{{ $i->equipements_texte }}</td>
                            <td>
                                {{ $i->titre }}
                                @if ($i->priorite->value === 'urgente')
                                    <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis ms-1">Urgente</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $i->date_signalement->format('d/m/Y') }}</td>
                            <td><span class="badge rounded-pill bg-{{ $c }}-subtle text-{{ $c }}-emphasis">{{ $i->statut->label() }}</span></td>
                            <td>{{ $i->technicien?->nom ?? '—' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('interventions.show', $i) }}" class="btn-icon view" title="Afficher"><i class="bi bi-eye-fill"></i></a>
                                <a href="{{ route('interventions.edit', $i) }}" class="btn-icon edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>
                                <form method="POST" action="{{ route('interventions.destroy', $i) }}" class="d-inline"
                                      onsubmit="return confirm('Supprimer cette intervention ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon delete" title="Supprimer"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">Aucune intervention pour le moment.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between small text-muted">
                <span>{{ $recentes->count() }} dernière(s) intervention(s)</span>
                <a href="{{ route('interventions.index') }}" class="fw-semibold">Tout voir <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- ===== Techniciens ===== --}}
    @can('admin')
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-gear me-2 text-primary"></i>Charge des techniciens
            </div>
            <div class="list-group list-group-flush">
                @forelse ($parTechnicien as $t)
                    <div class="list-group-item d-flex align-items-center gap-3 py-3">
                        <div class="avatar" style="width:38px;height:38px;background:#dbeafe;color:#1d4ed8">
                            {{ strtoupper(mb_substr($t->nom, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ route('techniciens.show', $t) }}" class="fw-medium text-dark">{{ $t->nom }}</a>
                            <div class="small text-muted">{{ $t->terminees_count }} terminée(s)</div>
                        </div>
                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">{{ $t->en_cours_count }} en cours</span>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-4">Aucun technicien.</div>
                @endforelse
            </div>
            <div class="card-footer bg-white small text-muted">
                {{ $stats['clients'] }} client(s) · {{ $stats['techniciens'] }} technicien(s)
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
