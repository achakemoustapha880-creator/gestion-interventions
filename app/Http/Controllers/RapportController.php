<?php

namespace App\Http\Controllers;

use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Models\Client;
use App\Models\Equipement;
use App\Models\Intervention;
use App\Models\Technicien;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    public function __invoke(Request $request)
    {
        // Période : par défaut les 6 derniers mois
        $du = $request->filled('du') ? Carbon::parse($request->du)->startOfDay() : now()->subMonths(5)->startOfMonth();
        $au = $request->filled('au') ? Carbon::parse($request->au)->endOfDay()   : now()->endOfDay();

        if ($du->gt($au)) {
            [$du, $au] = [$au->copy()->startOfDay(), $du->copy()->endOfDay()];
        }

        $periode = fn ($q) => $q->whereBetween('date_signalement', [$du->toDateString(), $au->toDateString()]);

        $interventions = Intervention::query()->tap($periode)->get();

        // ----- Chiffres clés -----
        $total   = $interventions->count();
        $termine = $interventions->where('statut', StatutIntervention::Termine)->count();
        $annule  = $interventions->where('statut', StatutIntervention::Annule)->count();
        $actives = $total - $termine - $annule;

        $durees = $interventions
            ->where('statut', StatutIntervention::Termine)
            ->filter(fn ($i) => $i->date_debut && $i->date_fin)
            ->map(fn ($i) => abs($i->date_debut->diffInMinutes($i->date_fin)));

        $dureeMoyenne = $durees->isNotEmpty() ? $durees->avg() : null;

        $stats = [
            'total'         => $total,
            'termine'       => $termine,
            'annule'        => $annule,
            'actives'       => $actives,
            'taux'          => $total > 0 ? round($termine * 100 / $total) : 0,
            'duree_moyenne' => $dureeMoyenne === null ? '—' : $this->formaterDuree($dureeMoyenne),
        ];

        // ----- Graphique : interventions par mois -----
        $mois = [];
        $curseur = $du->copy()->startOfMonth();
        while ($curseur->lte($au)) {
            $cle = $curseur->format('Y-m');
            $mois[$cle] = [
                'label'    => $curseur->copy()->locale('fr')->translatedFormat('M Y'),
                'creees'   => 0,
                'terminees' => 0,
            ];
            $curseur->addMonth();
        }
        foreach ($interventions as $i) {
            $cle = $i->date_signalement->format('Y-m');
            if (isset($mois[$cle])) {
                $mois[$cle]['creees']++;
                if ($i->statut === StatutIntervention::Termine) {
                    $mois[$cle]['terminees']++;
                }
            }
        }
        $parMois = [
            'labels'    => array_column($mois, 'label'),
            'creees'    => array_column($mois, 'creees'),
            'terminees' => array_column($mois, 'terminees'),
        ];

        // ----- Graphique : par type -----
        $parType = ['labels' => [], 'data' => []];
        foreach ($interventions->groupBy(fn ($i) => $i->type_intervention->value) as $valeur => $groupe) {
            $parType['labels'][] = TypeIntervention::from($valeur)->label();
            $parType['data'][]   = $groupe->count();
        }

        // ----- Graphique : par technicien -----
        $techniciens = Technicien::withCount([
            'interventions as total_count'     => $periode,
            'interventions as terminees_count' => fn ($q) => $periode($q)->where('statut', StatutIntervention::Termine->value),
        ])->orderBy('nom')->get();

        $parTechnicien = [
            'labels'    => $techniciens->pluck('nom')->all(),
            'total'     => $techniciens->pluck('total_count')->all(),
            'terminees' => $techniciens->pluck('terminees_count')->all(),
        ];

        // ----- Classements -----
        $topEquipements = Equipement::withCount(['interventions as n' => $periode])
    ->orderByDesc('n')->take(5)->get()->filter(fn ($e) => $e->n > 0);

        $topClients = Client::withCount(['interventions as n' => $periode])
            ->orderByDesc('n')->take(5)->get()->filter(fn ($c) => $c->n > 0);

        return view('rapports.index', compact(
            'du', 'au', 'stats', 'parMois', 'parType', 'parTechnicien', 'topEquipements', 'topClients'
        ));
    }

    private function formaterDuree(float $minutes): string
    {
        $minutes = (int) round($minutes);

        if ($minutes < 60) {
            return $minutes . ' min';
        }
        if ($minutes < 1440) {
            return floor($minutes / 60) . ' h ' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
        }

        return floor($minutes / 1440) . ' j ' . floor(($minutes % 1440) / 60) . ' h';
    }
}