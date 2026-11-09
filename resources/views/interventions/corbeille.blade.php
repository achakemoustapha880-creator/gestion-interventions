@extends('layouts.principal')
@section('titre', 'Corbeille')
@section('contenu')

@include('partials.onglets-corbeille')

<div class="card">
    <div class="card-header bg-white d-flex align-items-center flex-wrap gap-2">
        <div class="fw-semibold"><i class="bi bi-trash3 me-2 text-danger"></i>Interventions supprimées</div>

        @if ($interventions->total() > 0)
            <form method="POST" action="{{ route('interventions.vider-corbeille') }}" class="ms-auto"
                  onsubmit="return confirm('Vider la corbeille ? Cette action est définitive.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-octagon me-1"></i>Vider la corbeille</button>
            </form>
        @endif
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('interventions.corbeille') }}" data-live class="row g-2">
            <div class="col-md-6">
                <input name="q" value="{{ request('q') }}" class="form-control" autocomplete="off"
                       placeholder="Rechercher un problème ou un client…">
            </div>
        </form>
    </div>

    <div id="resultats">
        <form method="POST" action="{{ route('interventions.restaurer') }}">
            @csrf

            <div class="d-flex align-items-center flex-wrap gap-3 px-3 py-2 border-bottom bg-light">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="tout-selectionner">
                    <label class="form-check-label" for="tout-selectionner">Tout sélectionner</label>
                </div>

                <button type="submit" id="btn-restaurer" class="btn btn-sm btn-success" disabled>
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurer <span class="compteur"></span>
                </button>

                <button type="submit" id="btn-supprimer" class="btn btn-sm btn-danger" disabled
                        name="_method" value="DELETE"
                        formaction="{{ route('interventions.supprimer-definitivement') }}"
                        onclick="return confirm('Supprimer définitivement la sélection ? Impossible de la récupérer ensuite.')">
                    <i class="bi bi-trash me-1"></i>Supprimer définitivement <span class="compteur"></span>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px"></th>
                            <th>Client</th><th>Équipement</th><th>Problème</th><th>Statut</th><th>Supprimée le</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($interventions as $i)
                        @php $c = $i->statut->couleur(); @endphp
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $i->id }}" class="form-check-input ligne-check"></td>
                            <td class="fw-medium">{{ $i->client->nom }}</td>
                            <td>{{ $i->equipements_texte }}</td>
                            <td>{{ $i->titre }}</td>
                            <td><span class="badge rounded-pill bg-{{ $c }}-subtle text-{{ $c }}-emphasis">{{ $i->statut->label() }}</span></td>
                            <td class="text-muted small">{{ $i->deleted_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-trash3 fs-3 d-block mb-2"></i>La corbeille est vide.
                        </td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">{{ $interventions->total() }} intervention(s) dans la corbeille</span>
            {{ $interventions->links() }}
        </div>
    </div>
</div>

<script>
    function majSelection() {
        const cases   = document.querySelectorAll('.ligne-check');
        const cochees = document.querySelectorAll('.ligne-check:checked');
        const tout    = document.getElementById('tout-selectionner');

        ['btn-restaurer', 'btn-supprimer'].forEach(id => {
            const b = document.getElementById(id);
            if (b) {
                b.disabled = cochees.length === 0;
                const c = b.querySelector('.compteur');
                if (c) c.textContent = cochees.length ? '(' + cochees.length + ')' : '';
            }
        });

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