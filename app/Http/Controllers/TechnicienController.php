<?php

namespace App\Http\Controllers;

use App\Models\Technicien;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TechnicienController extends Controller
{
   public function index(Request $request)
{
    $techniciens = Technicien::withCount('interventions')
        ->when($request->q, function ($query, $s) {
            $query->where(function ($w) use ($s) {
                $w->where('nom', 'like', "%$s%")
                  ->orWhere('telephone', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            });
        })
        ->orderBy('nom')->paginate(15)->withQueryString();

    return view('techniciens.index', compact('techniciens'));
}

    public function create()
    {
        return view('techniciens.form', ['technicien' => new Technicien]);
    }

    public function store(Request $request)
    {
        Technicien::create($this->valider($request));
        return redirect()->route('techniciens.index')->with('success', 'Technicien enregistré.');
    }

    public function show(Technicien $technicien)
    {
        $interventions = $technicien->interventions()->with('client')
            ->latest('date_signalement')->get();

        return view('techniciens.show', compact('technicien', 'interventions'));
    }

    public function edit(Technicien $technicien)
    {
        return view('techniciens.form', compact('technicien'));
    }

    public function update(Request $request, Technicien $technicien)
    {
        $technicien->update($this->valider($request, $technicien));
        return redirect()->route('techniciens.index')->with('success', 'Technicien mis à jour.');
    }

    public function destroy(Technicien $technicien)
    {
        $technicien->delete(); // les interventions gardent leur historique (technicien_id = null)
        return redirect()->route('techniciens.index')->with('success', 'Technicien supprimé.');
    }

   private function valider(Request $request, ?Technicien $technicien = null): array
{
    return $request->validate([
        'nom'       => 'required|string|max:255',
        'telephone' => 'nullable|string|max:30',
        'email'     => ['required', 'email', Rule::unique('techniciens', 'email')->ignore($technicien)],
    ]);
}
}