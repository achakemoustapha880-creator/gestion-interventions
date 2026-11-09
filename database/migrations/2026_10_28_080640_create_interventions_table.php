<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('interventions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained()->restrictOnDelete();
        $table->foreignId('technicien_id')->nullable()->constrained('techniciens')->nullOnDelete();
        $table->string('titre');
        $table->text('description')->nullable();
        $table->string('statut')->default('en_attente');
        $table->date('date_signalement');
        $table->dateTime('date_debut')->nullable();
        $table->dateTime('date_fin')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
