<?php

namespace App\Enums;

enum TypeIntervention: string
{
    case Maintenance  = 'maintenance';
    case Reparation   = 'reparation';
    case Installation = 'installation';
    case Diagnostic   = 'diagnostic';

    public function label(): string
    {
        return match ($this) {
            self::Maintenance  => 'Maintenance',
            self::Reparation   => 'Réparation',
            self::Installation => 'Installation',
            self::Diagnostic   => 'Diagnostic',
        };
    }
}