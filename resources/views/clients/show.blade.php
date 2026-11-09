@extends('layouts.principal')
@section('titre', $client->nom)
@section('contenu')

@if ($client->trashed())
    <div class="alert alert-warning d-flex align-items-center flex-wrap gap-2">
        <i class="bi bi-trash3-fill me-1"></i>
        Ce client est dans la corbeille.
        @can('admin')
            <form method="POST" action="{{ route('clients.restaurer') }}" class="ms-auto">
                @csrf
                <input type="hidden" name="ids[]" value="{{ $client->id }}">
                <button class="btn btn-sm btn-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restaurer</button>
            </form>
        @endcan
    </div>
@endif

<div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-start gap-3">
        <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-person-fill"></i></div>
        <div>
            <h1 class="h4 fw-bold mb-1">{{ $client->nom }}</h1>
            <div class="text-muted">
                <i class="bi bi-telephone me-1"></i>{{ $client->telephone }}
                · <i class="bi bi-geo-alt me-1"></i>{{ $client->adresse ?? 'Adresse non renseignée' }}
            </div>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('interventions.create', ['client_id' => $client->id]) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Nouvelle intervention
            </a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-clock-history me-2 text-primary"></i>Historique des interventions ({{ $interventions->count() }})
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>N°</th><th>Date</th><th>Équipements</th><th>Problème</th><th>Technicien</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($interventions as $i)
                @php $c = $i->statut->couleur(); @endphp
                <tr>
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td class="text-muted small">{{ $i->date_signalement->format('d/m/Y') }}</td>
                    <td>{{ $i->equipements_texte }}</td>
                    <td class="fw-medium">{{ $i->titre }}</td>
                    <td>{{ $i->technicien?->nom ?? '—' }}</td>
                    <td><span class="badge rounded-pill bg-{{ $c }}-subtle text-{{ $c }}-emphasis">{{ $i->statut->label() }}</span></td>
                    <td class="text-end"><a href="{{ route('interventions.show', $i) }}" class="btn-icon view" title="Voir"><i class="bi bi-eye-fill"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Aucune intervention.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection