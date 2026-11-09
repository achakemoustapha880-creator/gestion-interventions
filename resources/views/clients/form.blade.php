@extends('layouts.principal')
@section('titre', 'Client')
@section('contenu')
<h1 class="h3 mb-3">{{ $client->exists ? 'Modifier le client' : 'Nouveau client' }}</h1>

<form method="POST" class="card card-body shadow-sm"
      action="{{ $client->exists ? route('clients.update', $client) : route('clients.store') }}">
    @csrf
    @if ($client->exists) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Nom *</label>
        <input name="nom" class="form-control" required value="{{ old('nom', $client->nom) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Téléphone *</label>
        <input name="telephone" class="form-control" required value="{{ old('telephone', $client->telephone) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Adresse</label>
        <input name="adresse" class="form-control" value="{{ old('adresse', $client->adresse) }}">
    </div>
    <div>
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('clients.index') }}" class="btn btn-link text-decoration-none">Annuler</a>
    </div>
</form>
@endsection