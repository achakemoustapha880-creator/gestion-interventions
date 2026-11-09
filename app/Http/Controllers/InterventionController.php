<?php

namespace App\Http\Controllers;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Models\Client;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\Technicien;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InterventionController extends Controller
{
    private function regles(): array
    {
        return [
            'client_id'         => ['required', Rule::exists('clients', 'id')->whereNull('deleted_at')],
            'technicien_id'     => 'nullable|exists:techniciens,id',
            'titre'             => 'required|string|max:255',
            'type_intervention' => ['required', Rule::enum(TypeIntervention::class)],
            'priorite'          => ['required', Rule::enum(PrioriteIntervention::class)],
            'description'       => 'nullable|string',
            'date_signalement'  => 'required|date',
            'equipements'       => 'nullable|array',
            'equipements.*'     => 'integer|exists:equipements,id',
            'quantites'         => 'nullable|array',
            'quantites.*'       => 'nullable|integer|min:1|max:9999',
        ];
    }

    // Un technicien ne peut créer ou modifier une intervention que pour lui-même
private function imposerTechnicien(array $data): array
{
    $user = auth()->user();

    if ($user->role === 'technicien') {
        $data['technicien_id'] = $user->technicien_id;
    }

    return $data;
}

// Texte de confirmation : indique si le technicien est prévenu par e-mail
private function messageTechnicien(Intervention $intervention): string
{
    $technicien = $intervention->technicien;

    if (! $technicien) {
        return '';
    }

    return $technicien->email
        ? " Un e-mail a été envoyé à {$technicien->nom} ({$technicien->email})."
        : " Attention : {$technicien->nom} n'a pas d'adresse e-mail, aucun message n'a été envoyé.";
}

    // Enregistre les équipements choisis et leurs quantités
    private function synchroniserEquipements(Intervention $intervention, Request $request): void
    {
        $sync = [];
        foreach ((array) $request->input('equipements', []) as $id) {
            $sync[(int) $id] = ['quantite' => max(1, (int) $request->input("quantites.$id", 1))];
        }
        $intervention->equipements()->sync($sync);
    }

    // Interventions actives : en attente + en cours
    public function index(Request $request)
    {
        $interventions = Intervention::with(['client', 'technicien', 'equipements'])
            ->whereIn('statut', [
                StatutIntervention::EnAttente->value,
                StatutIntervention::EnCours->value,
            ])
            ->when($request->q, function ($query, $s) {
                $query->where(function ($w) use ($s) {
                    $w->where('titre', 'like', "%$s%")
                      ->orWhereHas('client', fn ($c) => $c->where('nom', 'like', "%$s%"))
                      ->orWhereHas('technicien', fn ($t) => $t->where('nom', 'like', "%$s%"))
                      ->orWhereHas('equipements', fn ($e) => $e->where('nom', 'like', "%$s%"));
                });
            })
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->when($request->priorite, fn ($q, $p) => $q->where('priorite', $p))
            ->when($request->technicien_id, fn ($q, $id) => $q->where('technicien_id', $id))
            ->orderBy('id')
            ->paginate(15)->withQueryString();

        $techniciens = Technicien::orderBy('nom')->get();

        return view('interventions.index', compact('interventions', 'techniciens'));
    }

    // Historique : terminées + annulées
    private function requeteHistorique(Request $request)
    {
        return Intervention::with(['client', 'technicien', 'equipements'])
            ->whereIn('statut', [
                StatutIntervention::Termine->value,
                StatutIntervention::Annule->value,
            ])
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->when($request->q, function ($query, $s) {
                $query->where(function ($w) use ($s) {
                    $w->where('titre', 'like', "%$s%")
                      ->orWhereHas('client', fn ($c) => $c->where('nom', 'like', "%$s%"))
                      ->orWhereHas('equipements', fn ($e) => $e->where('nom', 'like', "%$s%"));
                });
            })
            ->when($request->du, fn ($q, $d) => $q->whereDate('date_fin', '>=', $d))
            ->when($request->au, fn ($q, $d) => $q->whereDate('date_fin', '<=', $d))
            ->orderBy('id');
    }

    public function historique(Request $request)
    {
        $interventions = $this->requeteHistorique($request)->paginate(15)->withQueryString();

        return view('interventions.historique', compact('interventions'));
    }

    public function exporter(Request $request)
    {
        $interventions = $this->requeteHistorique($request)->get();

        return response()->streamDownload(function () use ($interventions) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // accents corrects dans Excel

            fputcsv($out, ['N°', 'Problème', 'Type', 'Client', 'Téléphone', 'Équipements', 'Technicien', 'Priorité', 'Statut', 'Signalée le', 'Début', 'Clôturée le'], ';', '"', '\\');

            foreach ($interventions as $n => $i) {
                fputcsv($out, [
                    $n + 1,
                    $i->titre,
                    $i->type_intervention->label(),
                    $i->client->nom,
                    $i->client->telephone,
                    $i->equipements->map(fn ($e) => $e->nom . ' x' . $e->pivot->quantite)->implode(', '),
                    $i->technicien?->nom ?? '',
                    $i->priorite->label(),
                    $i->statut->label(),
                    $i->date_signalement->format('d/m/Y'),
                    $i->date_debut?->format('d/m/Y H:i'),
                    $i->date_fin?->format('d/m/Y H:i'),
                ], ';', '"', '\\');
            }

            fclose($out);
        }, 'historique-interventions-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create(Request $request)
    {
        return view('interventions.form', [
            'intervention' => new Intervention([
                'date_signalement' => now(),
                'client_id'        => $request->client_id,
            ]),
            'clients'     => Client::orderBy('nom')->get(),
            'techniciens' => Technicien::orderBy('nom')->get(),
            'equipements' => Equipement::orderBy('nom')->get(),
            'types'       => Equipement::types(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->regles());
        $data['statut'] = StatutIntervention::EnAttente->value;
        $data = $this->imposerTechnicien($data);

        $intervention = Intervention::create(collect($data)->except(['equipements', 'quantites'])->all());
        $this->synchroniserEquipements($intervention, $request);

       return redirect()->route('interventions.index')
    ->with('success', 'Intervention enregistrée.' . $this->messageTechnicien($intervention->load('technicien')));
    }

    public function show(Intervention $intervention)
    {
        $intervention->load(['client', 'technicien', 'equipements']);
        return view('interventions.show', compact('intervention'));
    }

    public function fiche(Intervention $intervention)
    {
        $intervention->load(['client', 'technicien', 'equipements']);

        return view('interventions.fiche', compact('intervention'));
    }

    public function edit(Intervention $intervention)
    {
        return view('interventions.form', [
            'intervention' => $intervention->load('equipements'),
            'clients'      => Client::orderBy('nom')->get(),
            'techniciens'  => Technicien::orderBy('nom')->get(),
            'equipements'  => Equipement::orderBy('nom')->get(),
            'types'        => Equipement::types(),
        ]);
    }

    public function update(Request $request, Intervention $intervention)
    {
        $data = $request->validate($this->regles());
        $data = $this->imposerTechnicien($data);

        $intervention->update(collect($data)->except(['equipements', 'quantites'])->all());
        $this->synchroniserEquipements($intervention, $request);

       $prevenu = $intervention->wasChanged('technicien_id') && $intervention->technicien_id
    ? $this->messageTechnicien($intervention->load('technicien'))
    : '';

return redirect()->route('interventions.show', $intervention)
    ->with('success', 'Intervention mise à jour.' . $prevenu);
    }

    // Mise à la corbeille
    public function destroy(Intervention $intervention)
    {
        $intervention->delete();
        return redirect()->route('interventions.index')->with('success', 'Intervention mise à la corbeille.');
    }

    // Mise à la corbeille de plusieurs interventions depuis l'historique
    public function supprimerSelection(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], ['ids.required' => 'Sélectionnez au moins une intervention.']);

        $nombre = Intervention::whereIn('id', $data['ids'])
            ->whereIn('statut', [StatutIntervention::Termine->value, StatutIntervention::Annule->value])
            ->delete();

        return back()->with('success', $nombre . ' intervention(s) mise(s) à la corbeille.');
    }

    // ----- Corbeille -----
    public function corbeille(Request $request)
    {
        $interventions = Intervention::onlyTrashed()
            ->with(['client', 'technicien', 'equipements'])
            ->when($request->q, function ($query, $s) {
                $query->where(function ($w) use ($s) {
                    $w->where('titre', 'like', "%$s%")
                      ->orWhereHas('client', fn ($c) => $c->where('nom', 'like', "%$s%"));
                });
            })
            ->latest('deleted_at')
            ->paginate(15)->withQueryString();

        return view('interventions.corbeille', compact('interventions'));
    }

    public function restaurer(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], ['ids.required' => 'Sélectionnez au moins une intervention.']);

        $nombre = Intervention::onlyTrashed()->whereIn('id', $data['ids'])->restore();

        return back()->with('success', $nombre . ' intervention(s) restaurée(s).');
    }

    public function supprimerDefinitivement(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], ['ids.required' => 'Sélectionnez au moins une intervention.']);

        $nombre = Intervention::onlyTrashed()->whereIn('id', $data['ids'])->forceDelete();

        return back()->with('success', $nombre . ' intervention(s) supprimée(s) définitivement.');
    }

    public function viderCorbeille()
    {
        $nombre = Intervention::onlyTrashed()->forceDelete();

        return back()->with('success', 'Corbeille vidée (' . $nombre . ' intervention(s)).');
    }

    // Changement de statut : renseigne automatiquement les dates
    public function changerStatut(Request $request, Intervention $intervention)
    {
        $request->validate(['statut' => ['required', Rule::enum(StatutIntervention::class)]]);
        $nouveau = StatutIntervention::from($request->statut);

        if ($nouveau === StatutIntervention::EnCours && ! $intervention->technicien_id) {
            return back()->with('error', 'Assignez un technicien avant de démarrer.');
        }

        $intervention->statut = $nouveau;

        if ($nouveau === StatutIntervention::EnCours) {
            $intervention->date_debut ??= now();
            $intervention->date_fin = null;
        }
        if ($nouveau === StatutIntervention::Termine) {
            $intervention->date_debut ??= now();
            $intervention->date_fin = now();
        }
        if ($nouveau === StatutIntervention::Annule) {
            $intervention->date_fin = now();
        }
        if ($nouveau === StatutIntervention::EnAttente) {
            $intervention->date_debut = null;
            $intervention->date_fin = null;
        }

        $intervention->save();

        return back()->with('success', 'Statut : ' . $nouveau->label());
    }
}