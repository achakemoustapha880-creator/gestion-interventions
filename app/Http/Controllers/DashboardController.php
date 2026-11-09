<?php

namespace App\Http\Controllers;

use App\Enums\StatutIntervention;
use App\Models\Client;
use App\Models\Intervention;
use App\Models\Technicien;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $total     = Intervention::count();
        $termine   = Intervention::where('statut', StatutIntervention::Termine->value)->count();
        $enCours   = Intervention::where('statut', StatutIntervention::EnCours->value)->count();
        $enAttente = Intervention::where('statut', StatutIntervention::EnAttente->value)->count();
        $annule    = Intervention::where('statut', StatutIntervention::Annule->value)->count();

        $pct = fn (int $n) => $total > 0 ? round($n * 100 / $total) : 0;

        $stats = [
            'total'           => $total,
            'cette_semaine'   => Intervention::where('created_at', '>=', now()->startOfWeek())->count(),
            'termine'         => $termine,
            'en_cours'        => $enCours,
            'en_attente'      => $enAttente,
            'annule'          => $annule,
            'pct_termine'     => $pct($termine),
            'pct_en_cours'    => $pct($enCours),
            'pct_en_attente'  => $pct($enAttente),
            'pct_annule'      => $pct($annule),
            'sans_technicien' => Intervention::whereIn('statut', [
                                    StatutIntervention::EnAttente->value,
                                    StatutIntervention::EnCours->value,
                                 ])->whereNull('technicien_id')->count(),
            'clients'         => Client::count(),
            'techniciens'     => Technicien::count(),
        ];

        $recentes = Intervention::with(['client', 'technicien', 'equipements'])
            ->latest('id')->take(7)->get()
            ->sortBy('id')->values();

        $parTechnicien = Technicien::withCount([
            'interventions as en_cours_count'  => fn ($q) => $q->where('statut', StatutIntervention::EnCours->value),
            'interventions as terminees_count' => fn ($q) => $q->where('statut', StatutIntervention::Termine->value),
        ])->orderBy('nom')->get();

        return view('dashboard', compact('stats', 'recentes', 'parTechnicien'));
    }
}