<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semestres', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->unsignedTinyInteger('numero');
            $table->foreignId('classe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annee_academiques')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['classe_id', 'annee_academique_id', 'numero'], 'semestres_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semestres');
    }
};
