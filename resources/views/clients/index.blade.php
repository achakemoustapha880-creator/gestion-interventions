@extends('layouts.principal')
@section('titre', 'Clients')
@section('contenu')

<div class="card">
    <div class="card-header bg-white d-flex align-items-center">
        <div class="fw-semibold"><i class="bi bi-people-fill me-2 text-primary"></i>Liste des clients</div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm ms-auto">
            <i class="bi bi-plus-lg me-1"></i> Nouveau client
        </a>
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('clients.index') }}" data-live class="row g-2">
            <div class="col-md-6">
                <input name="q" value="{{ request('q') }}" class="form-control" autocomplete="off"
                       placeholder="Rechercher un nom ou un téléphone…">
            </div>
        </form>
    </div>

    <div id="resultats">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Nom</th><th>Téléphone</th><th>Adresse</th><th>Interventions</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                @forelse ($clients as $c)
                    <tr>
                        <td class="fw-medium">{{ $c->nom }}</td>
                        <td>{{ $c->telephone }}</td>
                        <td>{{ $c->adresse ?? '—' }}</td>
                        <td>{{ $c->interventions_count }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('clients.show', $c) }}" class="btn-icon view" title="Afficher"><i class="bi bi-eye-fill"></i></a>
                            <a href="{{ route('clients.edit', $c) }}" class="btn-icon edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>
                            @can('admin')
    <form method="POST" action="{{ route('clients.destroy', $c) }}" class="d-inline"
         onsubmit="return confirm('Mettre {{ addslashes($c->nom) }} à la corbeille ?\n\nSes {{ $c->interventions_count }} intervention(s) sont conservées et il pourra être restauré.')"
        @csrf @method('DELETE')
        <button class="btn-icon delete" title="Supprimer"><i class="bi bi-trash-fill"></i></button>
    </form>
@endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">Aucun client.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">{{ $clients->total() }} client(s)</span>
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection