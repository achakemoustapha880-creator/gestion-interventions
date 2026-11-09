@extends('layouts.principal')
@section('titre', 'Mon profil')
@section('contenu')

<div class="row g-4">
    <div class="col-lg-6">
        <form method="POST" action="{{ route('profil.update') }}" class="card">
            @csrf @method('PUT')
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-circle me-2 text-primary"></i>Informations personnelles
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nom *</label>
                    <input name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse e-mail *</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
                </div>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
            </div>
        </form>
    </div>

    <div class="col-lg-6">
        <form method="POST" action="{{ route('profil.password') }}" class="card">
            @csrf @method('PUT')
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-shield-lock me-2 text-primary"></i>Changer le mot de passe
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Mot de passe actuel *</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe * <small class="text-muted">(8 caractères minimum)</small></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmer le nouveau mot de passe *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-primary"><i class="bi bi-key me-1"></i>Modifier le mot de passe</button>
            </div>
        </form>
    </div>
</div>
@endsection