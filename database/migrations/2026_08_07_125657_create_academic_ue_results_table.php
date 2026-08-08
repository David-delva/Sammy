<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_ue_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ue_id')->constrained('ues')->cascadeOnDelete();
            $table->unsignedSmallInteger('total_matieres')->default(0);
            $table->decimal('moyenne_ue', 5, 2)->nullable();
            $table->boolean('valide')->default(false);
            $table->boolean('compense')->default(false);
            $table->unsignedTinyInteger('credits_acquis')->default(0);
            $table->timestamps();

            $table->unique(['eleve_id', 'ue_id'], 'academic_ue_results_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_ue_results');
    }
};
