<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')
                  ->constrained('eleves')
                  ->onDelete('cascade');
            $table->foreignId('matiere_id')
                  ->constrained('matieres')
                  ->onDelete('cascade');
            $table->foreignId('annee_academique_id')
                  ->constrained('annee_academiques')
                  ->onDelete('cascade');
            $table->decimal('note', 4, 2);
            $table->enum('type_devoir', ['devoir', 'composition']);
            $table->timestamps();

            // Index de performance pour les requêtes de calcul
            $table->index('eleve_id');
            $table->index('matiere_id');
            $table->index('annee_academique_id');
            // Index composite pour les lookups fréquents bulletin
            $table->index(['eleve_id', 'annee_academique_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
}; 