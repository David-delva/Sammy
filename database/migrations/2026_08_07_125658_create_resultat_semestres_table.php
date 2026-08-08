<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultat_semestres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('total_notes')->default(0);
            $table->unsignedSmallInteger('evaluated_subjects')->default(0);
            $table->decimal('total_points', 8, 2)->nullable();
            $table->unsignedSmallInteger('total_coefficients')->default(0);
            $table->decimal('moyenne_semestre', 5, 2)->nullable();
            $table->unsignedSmallInteger('credits_acquis')->default(0);
            $table->unsignedSmallInteger('credits_total')->default(0);
            $table->string('decision')->nullable();
            $table->timestamps();

            $table->unique(['eleve_id', 'semestre_id'], 'resultat_semestres_unique');
            $table->index(['classe_id', 'semestre_id', 'moyenne_semestre'], 'resultat_semestres_class_avg_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultat_semestres');
    }
};
