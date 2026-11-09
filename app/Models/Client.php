<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = ['nom', 'telephone', 'adresse'];

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}