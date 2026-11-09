@extends('layouts.principal')
@section('titre', 'Interventions')
@section('contenu')

<div class="card">
    <div class="card-header bg-white d-flex align-items-center flex-wrap gap-2">
        <div class="fw-semibold"><i class="bi bi-list-ul me-2 text-primary"></i>Liste des interventions</div>
        <a href="{{ route('interventions.create') }}" class="btn btn-primary btn-sm ms-auto">
            <i class="bi bi-plus-lg me-1"></i> Nouvelle intervention
        </a>
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('interventions.index') }}" data-live class="row g-2">
            <div class="col-lg-4">
                <input name="q" value="{{ request('q') }}" class="form-control" autocomplete="off"
                       placeholder="Rechercher un client, équipement, technicien…">
            </div>
            <div class="col-lg-2 col-md-4">
                <select name="statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                    <option value="en_cours"   @selected(request('statut') === 'en_cours')>En cours</option>
                     <option value="en_cours"   @selected(request('statut') === 'en_cours')>Terminée</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <select name="priorite" class="form-select">
                    <option value="">Toutes priorités</option>
                    <option value="urgente" @selected(request('priorite') === 'urgente')>Urgente</option>
                    <option value="normale" @selected(request('priorite') === 'normale')>Normale</option>
                    <option value="basse"   @selected(request('priorite') === 'basse')>Basse</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <select name="technicien_id" class="form-select">
                    <option value="">Tous techniciens</option>
                    @foreach ($techniciens as $t)
                        <option value="{{ $t->id }}" @selected(request('technicien_id') == $t->id)>{{ $t->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <a href="{{ route('interventions.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-lg me-1"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <div id="resultats">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>N°</th><th>Client</th><th>Équipement</th><th>Problème</th><th>Type</th>
                        <th>Date</th><th>Statut</th><th>Technicien</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($interventions as $i)
                    @php $c = $i->statut->couleur(); $p = $i->priorite; @endphp
                    <tr>
                        <td class="text-muted">{{ $interventions->firstItem() + $loop->index }}</td>
                        <td class="fw-medium">{{ $i->client->nom }}</td>
                        <td>{{ $i->equipements_texte }}</td>
                        <td>
                            {{ $i->titre }}
                            @if ($p->value !== 'normale')
                                <span class="badge rounded-pill bg-{{ $p->couleur() }}-subtle text-{{ $p->couleur() }}-emphasis ms-1">{{ $p->label() }}</span>
                            @endif
                        </td>
                        <td>{{ $i->type_intervention->label() }}</td>
                        <td class="text-muted small">{{ $i->date_signalement->format('d/m/Y') }}</td>
                        <td><span class="badge rounded-pill bg-{{ $c }}-subtle text-{{ $c }}-emphasis">{{ $i->statut->label() }}</span></td>
                        <td>
                            @if ($i->technicien)
                                {{ $i->technicien->nom }}
                            @else
                                <span class="text-danger small"><i class="bi bi-exclamation-circle"></i> Non assigné</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <form method="POST" action="{{ route('interventions.statut', $i) }}" class="d-inline">
                                @csrf @method('PATCH')
                                @if ($i->statut->value === 'en_attente')
                                    @if ($i->technicien_id)
                                        <button name="statut" value="en_cours" class="btn-icon start" title="Démarrer"><i class="bi bi-play-fill"></i></button>
                                    @else
                                        <button type="button" class="btn-icon start opacity-50" disabled title="Assignez d'abord un technicien"><i class="bi bi-play-fill"></i></button>
                                    @endif
                                @else
                                    <button name="statut" value="termine" class="btn-icon finish" title="Terminer"><i class="bi bi-check-lg"></i></button>
                                @endif
                            </form>

                            <a href="{{ route('interventions.show', $i) }}" class="btn-icon view" title="Afficher"><i class="bi bi-eye-fill"></i></a>
                            <a href="{{ route('interventions.edit', $i) }}" class="btn-icon edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>
                            @can('admin')
                                <form method="POST" action="{{ route('interventions.destroy', $i) }}" class="d-inline"
                                      onsubmit="return confirm('Supprimer cette intervention ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon delete" title="Supprimer"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">Aucune intervention.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">Affichage de {{ $interventions->firstItem() ?? 0 }} à {{ $interventions->lastItem() ?? 0 }} sur {{ $interventions->total() }} interventions</span>
            {{ $interventions->links() }}
        </div>
    </div>
</div>
@endsection