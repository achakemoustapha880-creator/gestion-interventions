<?php

namespace App\Models;
use App\Observers\InterventionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(InterventionObserver::class)]
class Intervention extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'technicien_id', 'titre', 'type_intervention',
        'description', 'statut', 'priorite', 'date_signalement', 'date_debut', 'date_fin',
    ];

    protected $casts = [
        'statut'            => StatutIntervention::class,
        'priorite'          => PrioriteIntervention::class,
        'type_intervention' => TypeIntervention::class,
        'date_signalement'  => 'date',
        'date_debut'        => 'datetime',
        'date_fin'          => 'datetime',
    ];

    protected static function booted(): void
{
    // Un technicien ne voit que les interventions qui lui sont assignées
    static::addGlobalScope('technicien', function (Builder $builder) {
        $user = auth()->user();

        if ($user && $user->role === 'technicien') {
            if ($user->technicien_id) {
                $builder->where($builder->getModel()->getTable() . '.technicien_id', $user->technicien_id);
            } else {
                $builder->whereRaw('1 = 0'); // compte non relié : aucune intervention visible
            }
        }
    });
}
    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function technicien()
    {
        return $this->belongsTo(Technicien::class);
    }

    public function equipements()
    {
        return $this->belongsToMany(Equipement::class)->withPivot('quantite');
    }

    // Texte prêt à afficher dans les tableaux : "PC, Câble réseau"
    public function getEquipementsTexteAttribute(): string
    {
        return $this->equipements->pluck('nom')->implode(', ') ?: '—';
    }
}