<?php

namespace App\Enums;

enum PrioriteIntervention: string
{
    case Basse   = 'basse';
    case Normale = 'normale';
    case Urgente = 'urgente';

    public function label(): string
    {
        return match ($this) {
            self::Basse   => 'Basse',
            self::Normale => 'Normale',
            self::Urgente => 'Urgente',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Basse   => 'secondary',
            self::Normale => 'info',
            self::Urgente => 'danger',
        };
    }
}