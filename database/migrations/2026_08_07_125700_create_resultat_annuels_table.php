<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultat_annuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annee_academiques')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('total_notes')->default(0);
            $table->unsignedSmallInteger('evaluated_subjects')->default(0);
            $table->decimal('total_points', 8, 2)->nullable();
            $table->unsignedSmallInteger('total_coefficients')->default(0);
            $table->decimal('moyenne_annuelle', 5, 2)->nullable();
            $table->unsignedSmallInteger('credits_acquis')->default(0);
            $table->unsignedSmallInteger('credits_total')->default(0);
            $table->string('decision_jury')->nullable();
            $table->string('mention')->nullable();
            $table->timestamps();

            $table->unique(['eleve_id', 'annee_academique_id'], 'resultat_annuels_unique');
            $table->index(['classe_id', 'annee_academique_id', 'moyenne_annuelle'], 'resultat_annuels_class_avg_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultat_annuels');
    }
};
