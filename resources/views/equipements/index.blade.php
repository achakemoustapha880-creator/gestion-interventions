@extends('layouts.principal')
@section('titre', 'Équipements')
@section('contenu')

<div class="card">
    <div class="card-header bg-white d-flex align-items-center">
        <div class="fw-semibold"><i class="bi bi-pc-display me-2 text-primary"></i>Liste des équipements</div>
        <a href="{{ route('equipements.create') }}" class="btn btn-primary btn-sm ms-auto">
            <i class="bi bi-plus-lg me-1"></i> Nouvel équipement
        </a>
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('equipements.index') }}" data-live class="row g-2">
            <div class="col-md-6">
                <input name="q" value="{{ request('q') }}" class="form-control" autocomplete="off"
                       placeholder="Rechercher un nom, une référence, une marque, un modèle…">
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach ($types as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('equipements.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x-lg me-1"></i>Réinitialiser</a>
            </div>
        </form>
    </div>

    <div id="resultats">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>N°</th><th>Équipement</th><th>Référence</th><th>Type</th>
                        <th>Marque</th><th>Modèle</th><th>Notes</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($equipements as $e)
                    <tr>
                        <td class="text-muted">{{ $equipements->firstItem() + $loop->index }}</td>
                        <td class="fw-medium">{{ $e->nom }}</td>
                        <td class="text-muted small">{{ $e->reference ?? '—' }}</td>
                        <td>
                            @if ($e->type)
                                <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">{{ $e->type }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $e->marque ?? '—' }}</td>
                        <td>{{ $e->modele ?? '—' }}</td>
                        <td class="text-muted small">{{ $e->notes ? \Illuminate\Support\Str::limit($e->notes, 40) : '—' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('equipements.show', $e) }}" class="btn-icon view" title="Voir"><i class="bi bi-eye-fill"></i></a>
                            <a href="{{ route('equipements.edit', $e) }}" class="btn-icon edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>
                            @can('admin')
                                <form method="POST" action="{{ route('equipements.destroy', $e) }}" class="d-inline"
                                      onsubmit="return confirm('Supprimer cet équipement ? Il sera retiré des {{ $e->interventions_count }} intervention(s) qui l\'utilisent.')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon delete" title="Supprimer"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">Aucun équipement.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">{{ $equipements->total() }} équipement(s)</span>
            {{ $equipements->links() }}
        </div>
    </div>
</div>
@endsection