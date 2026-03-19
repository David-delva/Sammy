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
        Schema::table('notes', function (Blueprint $table) {
            $table->index('annee_academique_id');
            $table->index('matiere_id');
            $table->index('eleve_id');
        });

        Schema::table('inscriptions', function (Blueprint $table) {
            $table->index('annee_academique_id');
            $table->index('classe_id');
            $table->index('eleve_id');
        });

        Schema::table('matieres', function (Blueprint $table) {
            $table->index('classe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex(['annee_academique_id']);
            $table->dropIndex(['matiere_id']);
            $table->dropIndex(['eleve_id']);
        });

        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropIndex(['annee_academique_id']);
            $table->dropIndex(['classe_id']);
            $table->dropIndex(['eleve_id']);
        });

        Schema::table('matieres', function (Blueprint $table) {
            $table->dropIndex(['classe_id']);
        });
    }
};
