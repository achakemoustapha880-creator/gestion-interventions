@extends('layouts.principal')
@section('titre', 'Corbeille')
@section('contenu')

@include('partials.onglets-corbeille')

<div class="card">
    <div class="card-header bg-white d-flex align-items-center flex-wrap gap-2">
        <div class="fw-semibold"><i class="bi bi-trash3 me-2 text-danger"></i>Clients supprimés</div>

        @if ($clients->total() > 0)
            <form method="POST" action="{{ route('clients.vider-corbeille') }}" class="ms-auto"
                  onsubmit="return confirm('Vider la corbeille ? Les clients, leurs équipements et leurs interventions seront supprimés définitivement.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-octagon me-1"></i>Vider la corbeille</button>
            </form>
        @endif
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('clients.corbeille') }}" data-live class="row g-2">
            <div class="col-md-6">
                <input name="q" value="{{ request('q') }}" class="form-control" autocomplete="off"
                       placeholder="Rechercher un nom ou un téléphone…">
            </div>
        </form>
    </div>

    <div id="resultats">
        <form method="POST" action="{{ route('clients.restaurer') }}">
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
                        formaction="{{ route('clients.supprimer-definitivement') }}"
                        onclick="return confirm('Supprimer définitivement la sélection, avec leurs équipements et leurs interventions ? Impossible de les récupérer ensuite.')">
                    <i class="bi bi-trash me-1"></i>Supprimer définitivement <span class="compteur"></span>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px"></th>
                            <th>Nom</th><th>Téléphone</th><th>Adresse</th>
                            <th>Interventions</th><th>Supprimé le</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($clients as $c)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $c->id }}" class="form-check-input ligne-check"></td>
                            <td class="fw-medium">{{ $c->nom }}</td>
                            <td>{{ $c->telephone }}</td>
                            <td>{{ $c->adresse ?? '—' }}</td>
                            <td>{{ $c->interventions_count }}</td>
                            <td class="text-muted small">{{ $c->deleted_at->format('d/m/Y H:i') }}</td>
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
            <span class="small text-muted">{{ $clients->total() }} client(s) dans la corbeille</span>
            {{ $clients->links() }}
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