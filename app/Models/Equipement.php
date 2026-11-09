<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    protected $fillable = ['nom', 'reference', 'type', 'marque', 'modele', 'notes'];

    public function interventions()
    {
        return $this->belongsToMany(Intervention::class)->withPivot('quantite');
    }

    // Suggestions de types (les types déjà saisis s'y ajoutent) : le champ reste libre
    public static function types(): array
    {
        return collect(['Ordinateur', 'Imprimante', 'Réseau', 'Serveur', 'Téléphone', 'Écran', 'Câble', 'Pièce détachée', 'Logiciel'])
            ->merge(static::whereNotNull('type')->distinct()->pluck('type'))
            ->unique()->sort()->values()->all();
    }
}