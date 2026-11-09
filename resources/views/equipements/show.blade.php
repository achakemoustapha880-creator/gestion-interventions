@extends('layouts.principal')
@section('titre', $equipement->nom)
@section('contenu')

<div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-start gap-3">
        <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-pc-display"></i></div>
        <div>
            <h1 class="h4 fw-bold mb-1">{{ $equipement->nom }}</h1>
            <div class="text-muted">
                {{ $equipement->type ?? 'Type non renseigné' }}
                · Réf. {{ $equipement->reference ?? '—' }}
                · {{ trim($equipement->marque.' '.$equipement->modele) ?: 'Marque non renseignée' }}
            </div>
            @if ($equipement->notes)
                <div class="mt-2 small text-muted">{!! nl2br(e($equipement->notes)) !!}</div>
            @endif
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('interventions.create', ['equipement_id' => $equipement->id]) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Nouvelle intervention
            </a>
            <a href="{{ route('equipements.edit', $equipement) }}" class="btn btn-outline-secondary btn-sm">Modifier</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-clock-history me-2 text-primary"></i>Interventions utilisant cet équipement ({{ $interventions->count() }})
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>N°</th><th>Date</th><th>Client</th><th>Problème</th><th>Quantité</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($interventions as $i)
                @php $c = $i->statut->couleur(); @endphp
                <tr>
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td class="text-muted small">{{ $i->date_signalement->format('d/m/Y') }}</td>
                    <td class="fw-medium">{{ $i->client->nom }}</td>
                    <td>{{ $i->titre }}</td>
                    <td>{{ $i->pivot->quantite }}</td>
                    <td><span class="badge rounded-pill bg-{{ $c }}-subtle text-{{ $c }}-emphasis">{{ $i->statut->label() }}</span></td>
                    <td class="text-end"><a href="{{ route('interventions.show', $i) }}" class="btn-icon view" title="Voir"><i class="bi bi-eye-fill"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Cet équipement n'a pas encore été utilisé.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection