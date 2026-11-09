<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technicien extends Model
{
    protected $table = 'techniciens';
    protected $fillable = ['nom', 'telephone', 'email'];

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}