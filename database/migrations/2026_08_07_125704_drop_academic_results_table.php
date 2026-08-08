<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('academic_results');
    }

    public function down(): void
    {
        Schema::create('academic_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annee_academiques')->cascadeOnDelete();
            $table->unsignedTinyInteger('period');
            $table->unsignedSmallInteger('total_notes')->default(0);
            $table->unsignedSmallInteger('evaluated_subjects')->default(0);
            $table->decimal('total_points', 8, 2)->nullable();
            $table->unsignedSmallInteger('total_coefficients')->default(0);
            $table->decimal('moyenne_generale', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['eleve_id', 'annee_academique_id', 'period'], 'academic_results_unique_period');
        });
    }
};
