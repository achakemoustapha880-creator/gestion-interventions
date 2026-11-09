<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('interventions', 'priorite')) {
            Schema::table('interventions', function (Blueprint $table) {
                $table->string('priorite')->default('normale')->after('statut');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('interventions', 'priorite')) {
            Schema::table('interventions', function (Blueprint $table) {
                $table->dropColumn('priorite');
            });
        }
    }
};