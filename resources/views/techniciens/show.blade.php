@extends('layouts.principal')
@section('titre', $technicien->nom)
@section('contenu')
<h1 class="h3">{{ $technicien->nom }}</h1>
<p class="text-muted">{{ $technicien->telephone ?? 'Téléphone non renseigné' }} · {{ $technicien->email ?? 'Email non renseigné' }}</p>

<h2 class="h5 mt-4">Interventions réalisées ou assignées ({{ $interventions->count() }})</h2>
<div class="card shadow-sm">
<table class="table mb-0">
    <thead class="table-light"><tr><th>Date</th><th>Problème</th><th>Client</th><th>Statut</th></tr></thead>
    <tbody>
    @forelse ($interventions as $i)
        <tr>
            <td>{{ $i->date_signalement->format('d/m/Y') }}</td>
            <td><a href="{{ route('interventions.show', $i) }}">{{ $i->titre }}</a></td>
            <td>{{ $i->client->nom }}</td>
            <td><span class="badge text-bg-{{ $i->statut->couleur() }}">{{ $i->statut->label() }}</span></td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-muted text-center py-3">Aucune intervention.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection