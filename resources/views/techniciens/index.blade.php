@extends('layouts.principal')
@section('titre', 'Techniciens')
@section('contenu')

<div class="card">
    <div class="card-header bg-white d-flex align-items-center">
        <div class="fw-semibold"><i class="bi bi-person-gear me-2 text-primary"></i>Liste des techniciens</div>
        <a href="{{ route('techniciens.create') }}" class="btn btn-primary btn-sm ms-auto">
            <i class="bi bi-plus-lg me-1"></i> Nouveau technicien
        </a>
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('techniciens.index') }}" data-live class="row g-2">
            <div class="col-md-6">
                <input name="q" value="{{ request('q') }}" class="form-control" autocomplete="off"
                       placeholder="Rechercher un nom, un téléphone ou un e-mail…">
            </div>
        </form>
    </div>

    <div id="resultats">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Nom</th><th>Téléphone</th><th>E-mail</th><th>Interventions</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                @forelse ($techniciens as $t)
                    <tr>
                        <td class="fw-medium">
                            <span class="avatar d-inline-flex me-2" style="width:32px;height:32px;font-size:.8rem;background:#dbeafe;color:#1d4ed8">{{ strtoupper(mb_substr($t->nom, 0, 1)) }}</span>
                            {{ $t->nom }}
                        </td>
                        <td>{{ $t->telephone ?? '—' }}</td>
                       <td>
    @if ($t->email)
        {{ $t->email }}
    @else
        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-exclamation-triangle me-1"></i>Pas d'e-mail
        </span>
    @endif
</td>
                        <td>{{ $t->interventions_count }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('techniciens.show', $t) }}" class="btn-icon view" title="Afficher"><i class="bi bi-eye-fill"></i></a>
                            <a href="{{ route('techniciens.edit', $t) }}" class="btn-icon edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>
                            @can('admin')
                                <form method="POST" action="{{ route('techniciens.destroy', $t) }}" class="d-inline"
                                      onsubmit="return confirm('Supprimer ce technicien ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon delete" title="Supprimer"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">Aucun technicien.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">{{ $techniciens->total() }} technicien(s)</span>
            {{ $techniciens->links() }}
        </div>
    </div>
</div>
@endsection