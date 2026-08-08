<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalite_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->nullable()->constrained('annee_academiques')->cascadeOnDelete();
            $table->decimal('penalite_par_heure', 6, 4)->default(0.01);
            $table->timestamps();

            $table->unique(['classe_id', 'annee_academique_id'], 'penalite_absences_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalite_absences');
    }
};
