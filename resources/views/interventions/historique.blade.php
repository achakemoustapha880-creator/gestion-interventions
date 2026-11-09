@extends('layouts.principal')
@section('titre', 'Historique')
@section('contenu')

<div class="card">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-clock-history me-2 text-primary"></i>Historique (terminées et annulées)
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('interventions.historique') }}" data-live class="row g-2">
            <div class="col-lg-4"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Rechercher un problème, client ou équipement…" autocomplete="off"></div>
            <div class="col-lg-2 col-md-4">
                <select name="statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="termine" @selected(request('statut') === 'termine')>Terminées</option>
                    <option value="annule"  @selected(request('statut') === 'annule')>Annulées</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4"><input type="date" name="du" value="{{ request('du') }}" class="form-control" title="Du"></div>
            <div class="col-lg-2 col-md-4"><input type="date" name="au" value="{{ request('au') }}" class="form-control" title="Au"></div>
            <div class="col-lg-2">
                <a href="{{ route('interventions.historique') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-lg me-1"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <div id="resultats">
        <form method="POST" action="{{ route('interventions.supprimer-selection') }}" id="form-suppression"
              onsubmit="return confirm('Déplacer les interventions sélectionnées dans la corbeille ?')"
            @csrf @method('DELETE')

            {{-- Barre d'actions --}}
            <div class="d-flex align-items-center flex-wrap gap-3 px-3 py-2 border-bottom bg-light">
                @can('admin')
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="tout-selectionner">
                        <label class="form-check-label" for="tout-selectionner">Tout sélectionner</label>
                    </div>
                    <button type="submit" id="btn-supprimer" class="btn btn-sm btn-danger" disabled>
                        <i class="bi bi-trash me-1"></i>Supprimer la sélection <span id="compteur"></span>
                    </button>
                @endcan
                <a href="{{ route('interventions.export', request()->query()) }}" class="btn btn-sm btn-outline-success ms-auto">
                    <i class="bi bi-file-earmark-excel me-1"></i>Exporter
                </a>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            @can('admin')<th style="width:40px"></th>@endcan
                            <th>N°</th><th>Client</th><th>Équipement</th><th>Problème</th><th>Type</th>
                            <th>Technicien</th><th>Statut</th><th>Clôturée le</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($interventions as $i)
                        @php $c = $i->statut->couleur(); @endphp
                        <tr>
                            @can('admin')
                                <td><input type="checkbox" name="ids[]" value="{{ $i->id }}" class="form-check-input ligne-check"></td>
                            @endcan
                            <td class="text-muted">{{ $interventions->firstItem() + $loop->index }}</td>
                            <td class="fw-medium">{{ $i->client->nom }}</td>
                            <td>{{ $i->equipements_texte }}</td>
                            <td>{{ $i->titre }}</td>
                            <td>{{ $i->type_intervention->label() }}</td>
                            <td>{{ $i->technicien?->nom ?? '—' }}</td>
                            <td><span class="badge rounded-pill bg-{{ $c }}-subtle text-{{ $c }}-emphasis">{{ $i->statut->label() }}</span></td>
                            <td class="text-muted small">{{ $i->date_fin?->format('d/m/Y H:i') }}</td>
                            <td class="text-end"><a href="{{ route('interventions.show', $i) }}" class="btn-icon view" title="Afficher"><i class="bi bi-eye-fill"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-5">Aucun résultat.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">{{ $interventions->total() }} intervention(s)</span>
            {{ $interventions->links() }}
        </div>
    </div>
</div>

<script>
    // Sélection multiple (fonctionne aussi après la recherche en direct)
    function majSelection() {
        const cases = document.querySelectorAll('.ligne-check');
        const cochees = document.querySelectorAll('.ligne-check:checked');
        const btn = document.getElementById('btn-supprimer');
        const compteur = document.getElementById('compteur');
        const tout = document.getElementById('tout-selectionner');

        if (btn) btn.disabled = cochees.length === 0;
        if (compteur) compteur.textContent = cochees.length ? '(' + cochees.length + ')' : '';
        if (tout) {
            tout.checked = cases.length > 0 && cochees.length === cases.length;
            tout.indeterminate = cochees.length > 0 && cochees.length < cases.length;
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target.matches('#tout-selectionner')) {
            document.querySelectorAll('.ligne-check').forEach(c => c.checked = e.target.checked);
        }
        if (e.target.matches('#tout-selectionner, .ligne-check')) majSelection();
    });
</script>
@endsection