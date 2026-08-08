<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annee_academiques')->cascadeOnDelete();
            $table->unsignedTinyInteger('semestre');
            $table->unsignedSmallInteger('heures')->default(0);
            $table->timestamps();

            $table->unique(['eleve_id', 'matiere_id', 'annee_academique_id', 'semestre'], 'absences_unique_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
