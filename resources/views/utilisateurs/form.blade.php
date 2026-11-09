@extends('layouts.principal')
@section('titre', $utilisateur->exists ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')
@section('contenu')

@php $soiMeme = $utilisateur->exists && $utilisateur->id === auth()->id(); @endphp

<form method="POST" class="card" style="max-width:640px"
      action="{{ $utilisateur->exists ? route('utilisateurs.update', $utilisateur) : route('utilisateurs.store') }}">
    @csrf
    @if ($utilisateur->exists) @method('PUT') @endif

    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-person-plus me-2 text-primary"></i>{{ $utilisateur->exists ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
    </div>

    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nom *</label>
            <input name="name" class="form-control" required value="{{ old('name', $utilisateur->name) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Adresse e-mail *</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email', $utilisateur->email) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Rôle *</label>
            <select name="role" id="role" class="form-select" {{ $soiMeme ? 'disabled' : '' }}>
                <option value="technicien" @selected(old('role', $utilisateur->role) === 'technicien')>Technicien</option>
                <option value="admin" @selected(old('role', $utilisateur->role) === 'admin')>Administrateur</option>
            </select>
            @if ($soiMeme)
                <input type="hidden" name="role" value="{{ $utilisateur->role }}">
                <div class="form-text">Vous ne pouvez pas modifier votre propre rôle.</div>
            @endif
        </div>

        <div class="mb-3" id="bloc-technicien">
            <label class="form-label">Fiche technicien liée *</label>
            <select name="technicien_id" class="form-select" {{ $soiMeme ? 'disabled' : '' }}>
                <option value="">— Choisir —</option>
                @foreach ($techniciens as $t)
                    <option value="{{ $t->id }}" @selected(old('technicien_id', $utilisateur->technicien_id) == $t->id)>{{ $t->nom }}</option>
                @endforeach
            </select>
            <div class="form-text">Ce technicien ne verra que les interventions qui lui sont assignées. La fiche doit exister dans le menu Techniciens.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe {{ $utilisateur->exists ? '' : '*' }}</label>
            <input type="password" name="password" class="form-control" {{ $utilisateur->exists ? '' : 'required' }}>
            <div class="form-text">
                {{ $utilisateur->exists ? 'Laissez vide pour conserver le mot de passe actuel. ' : '' }}8 caractères minimum.
            </div>
        </div>
    </div>

    <div class="card-footer bg-white">
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        <a href="{{ route('utilisateurs.index') }}" class="btn btn-link text-muted text-decoration-none">Annuler</a>
    </div>
</form>

<script>
    const roleSel = document.getElementById('role');
    const blocTech = document.getElementById('bloc-technicien');

    function majBloc() { blocTech.style.display = roleSel.value === 'technicien' ? '' : 'none'; }

    roleSel.addEventListener('change', majBloc);
    majBloc();
</script>
@endsection