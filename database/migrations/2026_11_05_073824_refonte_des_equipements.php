<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table de liaison : plusieurs équipements par intervention, avec quantité
        if (! Schema::hasTable('equipement_intervention')) {
            Schema::create('equipement_intervention', function (Blueprint $table) {
                $table->foreignId('intervention_id')->constrained()->cascadeOnDelete();
                $table->foreignId('equipement_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('quantite')->default(1);
                $table->primary(['intervention_id', 'equipement_id']);
            });
        }

        // 2. On reprend les liens existants, puis on retire l'ancienne colonne
        if (Schema::hasColumn('interventions', 'equipement_id')) {
            foreach (DB::table('interventions')->whereNotNull('equipement_id')->get(['id', 'equipement_id']) as $l) {
                DB::table('equipement_intervention')->insertOrIgnore([
                    'intervention_id' => $l->id,
                    'equipement_id'   => $l->equipement_id,
                    'quantite'        => 1,
                ]);
            }

            Schema::table('interventions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('equipement_id');
            });
        }

        // 3. Les équipements n'appartiennent plus à un client
        if (Schema::hasColumn('equipements', 'client_id')) {
            Schema::table('equipements', function (Blueprint $table) {
                $table->dropConstrainedForeignId('client_id');
            });
        }

        // 4. Le numéro de série (unique) devient une référence libre
        if (Schema::hasIndex('equipements', 'equipements_numero_serie_unique')) {
            Schema::table('equipements', function (Blueprint $table) {
                $table->dropUnique('equipements_numero_serie_unique');
            });
        }

        if (Schema::hasColumn('equipements', 'numero_serie') && ! Schema::hasColumn('equipements', 'reference')) {
            Schema::table('equipements', function (Blueprint $table) {
                $table->renameColumn('numero_serie', 'reference');
            });
        }

        if (! Schema::hasColumn('equipements', 'reference')) {
            Schema::table('equipements', function (Blueprint $table) {
                $table->string('reference')->nullable()->after('nom');
            });
        }

        // 5. Le type devient facultatif
        Schema::table('equipements', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Migration à sens unique : restaurez la sauvegarde de la base pour revenir en arrière
    }
};