<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    private function regles(): array
    {
        return [
            'nom'       => 'required|string|max:255',
            'telephone' => 'required|string|max:30',
            'adresse'   => 'nullable|string|max:255',
        ];
    }

    public function index(Request $request)
    {
        $clients = Client::withCount('interventions')
            ->when($request->q, function ($query, $s) {
                $query->where(function ($w) use ($s) {
                    $w->where('nom', 'like', "%$s%")
                      ->orWhere('telephone', 'like', "%$s%");
                });
            })
            ->orderBy('nom')->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.form', ['client' => new Client]);
    }

    public function store(Request $request)
    {
        Client::create($request->validate($this->regles()));
        return redirect()->route('clients.index')->with('success', 'Client enregistré.');
    }

    public function show(Client $client)
    {
        $interventions = $client->interventions()->with(['technicien', 'equipements'])
            ->latest('date_signalement')->get();

        return view('clients.show', compact('client', 'interventions'));
    }

    public function edit(Client $client)
    {
        return view('clients.form', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $client->update($request->validate($this->regles()));
        return redirect()->route('clients.index')->with('success', 'Client mis à jour.');
    }

    // Mise à la corbeille : ses interventions sont conservées
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client mis à la corbeille.');
    }

    // ----- Corbeille -----
    public function corbeille(Request $request)
    {
        $clients = Client::onlyTrashed()
            ->withCount('interventions')
            ->when($request->q, function ($query, $s) {
                $query->where(function ($w) use ($s) {
                    $w->where('nom', 'like', "%$s%")
                      ->orWhere('telephone', 'like', "%$s%");
                });
            })
            ->latest('deleted_at')
            ->paginate(15)->withQueryString();

        return view('clients.corbeille', compact('clients'));
    }

    public function restaurer(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], ['ids.required' => 'Sélectionnez au moins un client.']);

        $nombre = Client::onlyTrashed()->whereIn('id', $data['ids'])->restore();

        return back()->with('success', $nombre . ' client(s) restauré(s).');
    }

    // Suppression définitive : efface aussi toutes les interventions du client
    public function supprimerDefinitivement(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], ['ids.required' => 'Sélectionnez au moins un client.']);

        $nombre = $this->effacer(Client::onlyTrashed()->whereIn('id', $data['ids'])->pluck('id')->all());

        return back()->with('success', $nombre . ' client(s) supprimé(s) définitivement.');
    }

    public function viderCorbeille()
    {
        $nombre = $this->effacer(Client::onlyTrashed()->pluck('id')->all());

        return back()->with('success', 'Corbeille vidée (' . $nombre . ' client(s)).');
    }

    private function effacer(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        DB::transaction(function () use ($ids) {
            Intervention::withTrashed()->whereIn('client_id', $ids)->forceDelete();
            Client::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        });

        return count($ids);
    }
}