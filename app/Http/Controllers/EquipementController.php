<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use Illuminate\Http\Request;

class EquipementController extends Controller
{
    private function regles(): array
    {
        return [
            'nom'       => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'type'      => 'nullable|string|max:100',
            'marque'    => 'nullable|string|max:255',
            'modele'    => 'nullable|string|max:255',
            'notes'     => 'nullable|string',
        ];
    }

    public function index(Request $request)
    {
        $equipements = Equipement::withCount('interventions')
            ->when($request->q, function ($query, $s) {
                $query->where(function ($w) use ($s) {
                    $w->where('nom', 'like', "%$s%")
                      ->orWhere('reference', 'like', "%$s%")
                      ->orWhere('marque', 'like', "%$s%")
                      ->orWhere('modele', 'like', "%$s%")
                      ->orWhere('type', 'like', "%$s%");
                });
            })
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->orderBy('nom')->paginate(15)->withQueryString();

        $types = Equipement::whereNotNull('type')->distinct()->orderBy('type')->pluck('type');

        return view('equipements.index', compact('equipements', 'types'));
    }

    public function create()
    {
        return view('equipements.form', ['equipement' => new Equipement, 'types' => Equipement::types()]);
    }

    public function store(Request $request)
    {
        $equipement = Equipement::create($request->validate($this->regles()));

        // Ajout rapide depuis le formulaire d'intervention
        if ($request->wantsJson()) {
            return response()->json($equipement, 201);
        }

        return redirect()->route('equipements.index')->with('success', 'Équipement enregistré.');
    }

    public function show(Equipement $equipement)
    {
        $interventions = $equipement->interventions()->with(['client', 'technicien'])
            ->latest('date_signalement')->get();

        return view('equipements.show', compact('equipement', 'interventions'));
    }

    public function edit(Equipement $equipement)
    {
        return view('equipements.form', ['equipement' => $equipement, 'types' => Equipement::types()]);
    }

    public function update(Request $request, Equipement $equipement)
    {
        $equipement->update($request->validate($this->regles()));
        return redirect()->route('equipements.index')->with('success', 'Équipement mis à jour.');
    }

    public function destroy(Equipement $equipement)
    {
        $equipement->delete(); // il est retiré des interventions qui l'utilisaient
        return redirect()->route('equipements.index')->with('success', 'Équipement supprimé.');
    }
}