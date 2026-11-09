@extends('layouts.principal')
@section('titre', $technicien->exists ? 'Modifier le technicien' : 'Nouveau technicien')
@section('contenu')

<form method="POST" class="card" style="max-width:640px"
      action="{{ $technicien->exists ? route('techniciens.update', $technicien) : route('techniciens.store') }}">
    @csrf
    @if ($technicien->exists) @method('PUT') @endif

    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-person-gear me-2 text-primary"></i>{{ $technicien->exists ? 'Modifier le technicien' : 'Nouveau technicien' }}
    </div>

    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nom *</label>
            <input name="nom" class="form-control" required value="{{ old('nom', $technicien->nom) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input name="telephone" class="form-control" value="{{ old('telephone', $technicien->telephone) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">E-mail *</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email', $technicien->email) }}">
            <div class="form-text">Le technicien reçoit un message à cette adresse quand une intervention lui est attribuée.</div>
        </div>
    </div>

    <div class="card-footer bg-white">
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        <a href="{{ route('techniciens.index') }}" class="btn btn-link text-muted text-decoration-none">Annuler</a>
    </div>
</form>
@endsection