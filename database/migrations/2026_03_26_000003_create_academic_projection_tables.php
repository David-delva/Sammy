<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
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
            $table->index(['classe_id', 'annee_academique_id', 'period'], 'academic_results_class_period_idx');
            $table->index(['classe_id', 'annee_academique_id', 'period', 'moyenne_generale'], 'academic_results_class_period_average_idx');
        });

        Schema::create('academic_subject_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annee_academique_id')->constrained('annee_academiques')->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('period');
            $table->unsignedTinyInteger('coefficient')->default(1);
            $table->unsignedSmallInteger('total_notes')->default(0);
            $table->decimal('moyenne_devoirs', 5, 2)->nullable();
            $table->decimal('note_composition', 5, 2)->nullable();
            $table->decimal('moyenne_matiere', 5, 2)->nullable();
            $table->decimal('moy_x_coef', 8, 2)->nullable();
            $table->timestamp('last_recorded_at')->nullable();
            $table->timestamps();

            $table->unique(['eleve_id', 'annee_academique_id', 'matiere_id', 'period'], 'academic_subject_results_unique_period');
            $table->index(['classe_id', 'annee_academique_id', 'period'], 'academic_subject_results_class_period_idx');
            $table->index(['eleve_id', 'annee_academique_id', 'period'], 'academic_subject_results_student_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_subject_results');
        Schema::dropIfExists('academic_results');
    }
};
