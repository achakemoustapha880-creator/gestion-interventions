<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            $table->foreignId('equipement_id')->nullable()->after('technicien_id')
                  ->constrained('equipements')->nullOnDelete();
            $table->string('type_intervention')->default('reparation')->after('titre');
        });
    }

    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipement_id');
            $table->dropColumn('type_intervention');
        });
    }
};