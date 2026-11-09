@extends('layouts.principal')
@section('titre', 'Intervention ')
@section('contenu')

@php $c = $intervention->statut->couleur(); $p = $intervention->priorite; @endphp

<div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center gap-3">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ $intervention->titre }}</h1>
            <span class="badge rounded-pill bg-{{ $c }}-subtle text-{{ $c }}-emphasis">{{ $intervention->statut->label() }}</span>
            <span class="badge rounded-pill bg-{{ $p->couleur() }}-subtle text-{{ $p->couleur() }}-emphasis">Priorité {{ strtolower($p->label()) }}</span>
            <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">{{ $intervention->type_intervention->label() }}</span>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('interventions.edit', $intervention) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>

            <a href="{{ route('interventions.fiche', $intervention) }}" target="_blank" class="btn btn-outline-primary btn-sm">
    <i class="bi bi-file-earmark-pdf me-1"></i>Fiche PDF
</a>
            <form method="POST" action="{{ route('interventions.destroy', $intervention) }}"
                  onsubmit="return confirm('Supprimer cette intervention ?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Supprimer</button>
            </form>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Informations</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Client</dt>
                    <dd class="col-sm-8">
                        <a href="{{ route('clients.show', $intervention->client) }}" class="fw-medium text-dark">{{ $intervention->client->nom }}</a>
                        <span class="text-muted">· {{ $intervention->client->telephone }}</span>
                    </dd>

                    <<dt class="col-sm-4 text-muted fw-normal">Équipements</dt>
<dd class="col-sm-8">
    @forelse ($intervention->equipements as $e)
        <div>
            <a href="{{ route('equipements.show', $e) }}" class="fw-medium text-dark">{{ $e->nom }}</a>
            <span class="text-muted">
                {{ $e->reference ? '· réf. '.$e->reference : '' }} · quantité {{ $e->pivot->quantite }}
            </span>
        </div>
    @empty
        —
    @endforelse
</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Technicien</dt>
                    <dd class="col-sm-8">{{ $intervention->technicien?->nom ?? 'Non assigné' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Signalée le</dt>
                    <dd class="col-sm-8">{{ $intervention->date_signalement->format('d/m/Y') }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Début</dt>
                    <dd class="col-sm-8">{{ $intervention->date_debut?->format('d/m/Y H:i') ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Fin</dt>
                    <dd class="col-sm-8">{{ $intervention->date_fin?->format('d/m/Y H:i') ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Description</dt>
                    <dd class="col-sm-8 mb-0">{!! nl2br(e($intervention->description ?? '—')) !!}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Changer le statut</div>
            <div class="card-body d-grid gap-2">
                <form method="POST" action="{{ route('interventions.statut', $intervention) }}" class="d-grid gap-2">
                    @csrf @method('PATCH')
                    <button name="statut" value="en_attente" class="btn btn-outline-warning"><i class="bi bi-clock me-1"></i>Remettre en attente</button>
                    <button name="statut" value="en_cours" class="btn btn-outline-primary"><i class="bi bi-play-fill me-1"></i>Démarrer / En cours</button>
                    <button name="statut" value="termine" class="btn btn-outline-success"><i class="bi bi-check-lg me-1"></i>Terminer</button>
                    <button name="statut" value="annule" class="btn btn-outline-danger"
        onclick="return confirm('Annuler cette intervention ?')">
      <i class="bi bi-x-circle me-1" ></i>Annuler l'intervention
        </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection