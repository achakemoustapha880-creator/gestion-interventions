@extends('layouts.principal')
@section('titre', 'Utilisateurs')
@section('contenu')

<div class="card">
    <div class="card-header bg-white d-flex align-items-center">
        <div class="fw-semibold"><i class="bi bi-people me-2 text-primary"></i>Utilisateurs de l'application</div>
        <a href="{{ route('utilisateurs.create') }}" class="btn btn-primary btn-sm ms-auto">
            <i class="bi bi-plus-lg me-1"></i> Nouvel utilisateur
        </a>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Créé le</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @foreach ($utilisateurs as $u)
                <tr>
                    <td class="fw-medium">
                        <span class="avatar d-inline-flex me-2" style="width:32px;height:32px;font-size:.8rem">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                        {{ $u->name }}
                        @if ($u->id === auth()->id())
                            <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis ms-1">Vous</span>
                        @endif
                    </td>
                    <td>{{ $u->email }}</td>
                  <td>
    @if ($u->role === 'admin')
        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">Administrateur</span>
    @else
        <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">Technicien</span>
    @endif
</td>  
                    <td class="text-muted small">{{ $u->created_at?->format('d/m/Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('utilisateurs.edit', $u) }}" class="btn-icon edit" title="Modifier"><i class="bi bi-pencil-fill"></i></a>
                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('utilisateurs.destroy', $u) }}" class="d-inline"
                                  onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button class="btn-icon delete" title="Supprimer"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection