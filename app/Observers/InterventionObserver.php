<?php

namespace App\Observers;

use App\Mail\InterventionAssignee;
use App\Models\Intervention;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InterventionObserver
{
    public function created(Intervention $intervention): void
    {
        Log::info('[MAIL] Intervention créée #' . $intervention->id . ', technicien_id = ' . var_export($intervention->technicien_id, true));

        if ($intervention->technicien_id) {
            $this->prevenirTechnicien($intervention->id);
        }
    }

    public function updated(Intervention $intervention): void
    {
        if ($intervention->wasChanged('technicien_id') && $intervention->technicien_id) {
            Log::info('[MAIL] Technicien changé sur #' . $intervention->id);
            $this->prevenirTechnicien($intervention->id);
        }
    }

    private function prevenirTechnicien(int $id): void
    {
        $envoyer = function () use ($id) {
            $i = Intervention::withoutGlobalScopes()->with(['client', 'technicien'])->find($id);

            if (! $i || ! $i->technicien) {
                Log::info('[MAIL] Aucun technicien trouvé pour #' . $id);
                return;
            }

            if (! $i->technicien->email) {
                Log::info('[MAIL] ' . $i->technicien->nom . ' n\'a pas d\'adresse e-mail : aucun message');
                return;
            }

            try {
                Mail::to($i->technicien->email)->send(new InterventionAssignee($i));
                Log::info('[MAIL] Message envoyé à ' . $i->technicien->email);
            } catch (\Throwable $e) {
                Log::error('[MAIL] E-mail non envoyé à ' . $i->technicien->email . ' : ' . $e->getMessage());
            }
        };

        // Envoi après l'affichage de la page
        function_exists('defer') ? defer($envoyer) : $envoyer();
    }
}