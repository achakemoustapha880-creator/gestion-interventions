@extends('layouts.principal')
@section('titre', $intervention->exists ? 'Modifier l\'intervention' : 'Nouvelle intervention')
@section('contenu')

@php
    $selectionnes = collect(old('equipements', $intervention->exists
            ? $intervention->equipements->pluck('id')->all()
            : array_filter([request('equipement_id')])))
        ->map(fn ($v) => (int) $v)->all();
    $quantites = $intervention->exists ? $intervention->equipements->pluck('pivot.quantite', 'id')->all() : [];
@endphp

<form method="POST" class="card"
      action="{{ $intervention->exists ? route('interventions.update', $intervention) : route('interventions.store') }}">
    @csrf
    @if ($intervention->exists) @method('PUT') @endif

    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-clipboard2-check me-2 text-primary"></i>{{ $intervention->exists ? 'Modifier l\'intervention' : 'Nouvelle intervention' }}
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Client *</label>
                <select name="client_id" class="form-select" required>
                    <option value="">— Choisir —</option>
                    @foreach ($clients as $c)
                        <option value="{{ $c->id }}" @selected(old('client_id', $intervention->client_id) == $c->id)>
                            {{ $c->nom }} ({{ $c->telephone }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
    <label class="form-label">Technicien</label>
    @can('admin')
        <select name="technicien_id" class="form-select">
            <option value="">— Non assigné —</option>
            @foreach ($techniciens as $t)
                <option value="{{ $t->id }}" @selected(old('technicien_id', $intervention->technicien_id) == $t->id)>{{ $t->nom }}</option>
            @endforeach
        </select>
    @else
        <input class="form-control" value="{{ auth()->user()->technicien?->nom ?? 'Non relié à une fiche' }}" disabled>
        <div class="form-text">L'intervention vous est automatiquement assignée.</div>
    @endcan
</div>

            <div class="col-md-6">
                <label class="form-label">Problème signalé *</label>
                <input type="text" name="titre" class="form-control" required
                       value="{{ old('titre', $intervention->titre) }}" placeholder="Ex : L'ordinateur ne démarre plus">
            </div>
            <div class="col-md-3">
                <label class="form-label">Type d'intervention *</label>
                <select name="type_intervention" class="form-select" required>
                    @foreach (\App\Enums\TypeIntervention::cases() as $t)
                        <option value="{{ $t->value }}"
                            @selected(old('type_intervention', $intervention->type_intervention?->value ?? 'reparation') === $t->value)>
                            {{ $t->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Priorité *</label>
                <select name="priorite" class="form-select" required>
                    @foreach (\App\Enums\PrioriteIntervention::cases() as $p)
                        <option value="{{ $p->value }}"
                            @selected(old('priorite', $intervention->priorite?->value ?? 'normale') === $p->value)>
                            {{ $p->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Date de signalement *</label>
                <input type="date" name="date_signalement" class="form-control" required
                       value="{{ old('date_signalement', optional($intervention->date_signalement)->format('Y-m-d')) }}">
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description', $intervention->description) }}</textarea>
            </div>

            {{-- ===== Équipements nécessaires ===== --}}
            <div class="col-12">
                <label class="form-label">Équipements nécessaires</label>

                <div class="border rounded-3 overflow-hidden">
                    <div class="p-2 border-bottom bg-light d-flex gap-2">
                        <input type="search" id="rech-equip" class="form-control form-control-sm" autocomplete="off"
                               placeholder="Rechercher dans les équipements…">
                        <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" id="btn-nouvel-equip">
                            <i class="bi bi-plus-lg me-1"></i>Nouvel équipement
                        </button>
                    </div>

                    {{-- Ajout rapide --}}
                    <div id="nouvel-equip" class="p-3 border-bottom bg-primary-subtle" style="display:none">
                        <div class="fw-semibold mb-2">Enregistrer un équipement absent de la liste</div>
                        <div class="row g-2">
                            <div class="col-md-4"><input id="ne-nom" class="form-control form-control-sm" placeholder="Nom *"></div>
                            <div class="col-md-4"><input id="ne-reference" class="form-control form-control-sm" placeholder="Référence"></div>
                            <div class="col-md-4"><input id="ne-type" class="form-control form-control-sm" list="types-equip" placeholder="Type" autocomplete="off"></div>
                            <div class="col-md-4"><input id="ne-marque" class="form-control form-control-sm" placeholder="Marque"></div>
                            <div class="col-md-4"><input id="ne-modele" class="form-control form-control-sm" placeholder="Modèle"></div>
                            <div class="col-md-4"><input id="ne-notes" class="form-control form-control-sm" placeholder="Notes"></div>
                        </div>
                        <datalist id="types-equip">
                            @foreach ($types as $t) <option value="{{ $t }}"> @endforeach
                        </datalist>
                        <div id="ne-erreur" class="text-danger small mt-2 d-none"></div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-primary" id="ne-enregistrer"><i class="bi bi-check-lg me-1"></i>Enregistrer et ajouter</button>
                            <button type="button" class="btn btn-sm btn-link text-muted" id="ne-annuler">Annuler</button>
                        </div>
                    </div>

                    {{-- Liste --}}
                    <div id="liste-equip" style="max-height:240px; overflow-y:auto">
                        @foreach ($equipements as $e)
                            <div class="equip-ligne d-flex align-items-center gap-3 px-3 py-2 border-bottom"
                                 data-nom="{{ \Illuminate\Support\Str::lower($e->nom.' '.$e->reference.' '.$e->type.' '.$e->marque.' '.$e->modele) }}">
                                <input type="checkbox" class="form-check-input equip-check" name="equipements[]" value="{{ $e->id }}"
                                       @checked(in_array($e->id, $selectionnes))>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $e->nom }}</div>
                                    <div class="small text-muted details">{{ collect([$e->reference ? 'Réf. '.$e->reference : null, $e->type, trim($e->marque.' '.$e->modele)])->filter()->implode(' · ') }}</div>
                                </div>
                                <input type="number" min="1" max="9999" class="form-control form-control-sm equip-qte" style="width:80px"
                                       name="quantites[{{ $e->id }}]" value="{{ old('quantites.'.$e->id, $quantites[$e->id] ?? 1) }}" title="Quantité">
                            </div>
                        @endforeach
                        <div id="aucun-equip" class="text-center text-muted py-3 {{ $equipements->count() ? 'd-none' : '' }}">
                            Aucun équipement enregistré : utilisez « Nouvel équipement ».
                        </div>
                    </div>
                </div>
                <div class="form-text">Cochez les équipements utilisés et indiquez la quantité de chacun.</div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white">
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        <a href="{{ route('interventions.index') }}" class="btn btn-link text-muted text-decoration-none">Annuler</a>
    </div>
</form>

<template id="modele-ligne">
    <div class="equip-ligne d-flex align-items-center gap-3 px-3 py-2 border-bottom">
        <input type="checkbox" class="form-check-input equip-check" name="equipements[]" checked>
        <div class="flex-grow-1">
            <div class="fw-medium nom"></div>
            <div class="small text-muted details"></div>
        </div>
        <input type="number" min="1" max="9999" class="form-control form-control-sm equip-qte" style="width:80px" value="1" title="Quantité">
    </div>
</template>

<script>
(function () {
    const liste = document.getElementById('liste-equip');
    const panneau = document.getElementById('nouvel-equip');
    const champs = ['nom', 'reference', 'type', 'marque', 'modele', 'notes'];
    const erreur = document.getElementById('ne-erreur');

    // Recherche dans la liste
    document.getElementById('rech-equip').addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        liste.querySelectorAll('.equip-ligne').forEach(l => {
            l.style.display = (l.dataset.nom || '').includes(q) ? '' : 'none';
        });
    });

    // Ouvrir / fermer le panneau d'ajout
    document.getElementById('btn-nouvel-equip').addEventListener('click', () => {
        panneau.style.display = panneau.style.display === 'none' ? '' : 'none';
        document.getElementById('ne-nom').focus();
    });
    document.getElementById('ne-annuler').addEventListener('click', () => { panneau.style.display = 'none'; });

    // Touche Entrée : enregistrer l'équipement (sans envoyer tout le formulaire)
    champs.forEach(c => document.getElementById('ne-' + c).addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('ne-enregistrer').click(); }
    }));

    function ajouterLigne(e) {
        const l = document.getElementById('modele-ligne').content.firstElementChild.cloneNode(true);
        const details = [e.reference ? 'Réf. ' + e.reference : '', e.type || '', [e.marque, e.modele].filter(Boolean).join(' ')]
            .filter(Boolean).join(' · ');

        l.dataset.nom = [e.nom, e.reference, e.type, e.marque, e.modele].filter(Boolean).join(' ').toLowerCase();
        l.querySelector('.equip-check').value = e.id;
        l.querySelector('.nom').textContent = e.nom;
        l.querySelector('.details').textContent = details;
        l.querySelector('.equip-qte').name = 'quantites[' + e.id + ']';

        liste.prepend(l);
        document.getElementById('aucun-equip').classList.add('d-none');
    }

    // Enregistrer un nouvel équipement sans quitter la page
    document.getElementById('ne-enregistrer').addEventListener('click', async function () {
        const data = {};
        champs.forEach(c => data[c] = document.getElementById('ne-' + c).value.trim());
        erreur.classList.add('d-none');

        if (!data.nom) {
            erreur.textContent = 'Le nom de l\'équipement est obligatoire.';
            erreur.classList.remove('d-none');
            return;
        }

        this.disabled = true;
        try {
            const r = await fetch(@json(route('equipements.store')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            const json = await r.json();

            if (!r.ok) {
                erreur.textContent = Object.values(json.errors || {}).flat().join(' ') || 'Erreur lors de l\'enregistrement.';
                erreur.classList.remove('d-none');
            } else {
                ajouterLigne(json);
                champs.forEach(c => document.getElementById('ne-' + c).value = '');
                panneau.style.display = 'none';
            }
        } catch (e) {
            erreur.textContent = 'Erreur de connexion, réessayez.';
            erreur.classList.remove('d-none');
        }
        this.disabled = false;
    });
})();
</script>
@endsection