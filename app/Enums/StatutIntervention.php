<?php

namespace App\Enums;

enum StatutIntervention: string
{
    case EnAttente = 'en_attente';
    case EnCours   = 'en_cours';
    case Termine   = 'termine';
    case Annule    = 'annule';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::EnCours   => 'En cours',
            self::Termine   => 'Terminée',
            self::Annule    => 'Annulée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EnAttente => 'warning',
            self::EnCours   => 'primary',
            self::Termine   => 'success',
            self::Annule    => 'danger',
        };
    }
}