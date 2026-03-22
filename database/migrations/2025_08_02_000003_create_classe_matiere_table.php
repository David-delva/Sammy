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
        Schema::create('classe_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')
                  ->constrained('classes')
                  ->onDelete('cascade');
            $table->foreignId('matiere_id')
                  ->constrained('matieres')
                  ->onDelete('cascade');
            $table->foreignId('annee_academique_id')
                  ->constrained('annee_academiques')
                  ->onDelete('cascade');
            $table->unsignedTinyInteger('coefficient')->default(1);
            $table->timestamps();

            // Une matière ne peut être liée qu'une fois par classe par année
            $table->unique(['classe_id', 'matiere_id', 'annee_academique_id']);

            // Index pour les requêtes fréquentes
            $table->index(['classe_id', 'annee_academique_id']);
            $table->index(['matiere_id', 'annee_academique_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classe_matiere');
    }
};
