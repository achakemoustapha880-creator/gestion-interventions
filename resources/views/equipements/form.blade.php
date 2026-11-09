@extends('layouts.principal')
@section('titre', $equipement->exists ? 'Modifier l\'équipement' : 'Nouvel équipement')
@section('contenu')

<form method="POST" class="card"
      action="{{ $equipement->exists ? route('equipements.update', $equipement) : route('equipements.store') }}">
    @csrf
    @if ($equipement->exists) @method('PUT') @endif

    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-pc-display me-2 text-primary"></i>{{ $equipement->exists ? 'Modifier l\'équipement' : 'Nouvel équipement' }}
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nom de l'équipement *</label>
                <input name="nom" class="form-control" required value="{{ old('nom', $equipement->nom) }}" placeholder="Ex : Câble réseau, Disque dur, Routeur…">
            </div>
            <div class="col-md-6">
                <label class="form-label">Référence</label>
                <input name="reference" class="form-control" value="{{ old('reference', $equipement->reference) }}" placeholder="Ex : RJ45-CAT6-5M">
            </div>
            <div class="col-md-4">
                <label class="form-label">Type</label>
                <input name="type" class="form-control" list="types-equip" autocomplete="off"
                       value="{{ old('type', $equipement->type) }}" placeholder="Choisissez ou écrivez un type">
                <datalist id="types-equip">
                    @foreach ($types as $t) <option value="{{ $t }}"> @endforeach
                </datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Marque</label>
                <input name="marque" class="form-control" value="{{ old('marque', $equipement->marque) }}" placeholder="Ex : HP">
            </div>
            <div class="col-md-4">
                <label class="form-label">Modèle</label>
                <input name="modele" class="form-control" value="{{ old('modele', $equipement->modele) }}" placeholder="Ex : ProBook 450">
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $equipement->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white">
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        <a href="{{ route('equipements.index') }}" class="btn btn-link text-muted text-decoration-none">Annuler</a>
    </div>
</form>
@endsection