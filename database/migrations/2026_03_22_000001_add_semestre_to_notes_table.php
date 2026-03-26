<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->unsignedTinyInteger('semestre')->default(1)->after('type_devoir');
        });

        DB::table('notes')->whereNull('semestre')->update(['semestre' => 1]);

        Schema::table('notes', function (Blueprint $table) {
            $table->index(['annee_academique_id', 'semestre'], 'notes_annee_semestre_index');
            $table->index(['eleve_id', 'matiere_id', 'annee_academique_id', 'semestre'], 'notes_lookup_semestre_index');
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex('notes_annee_semestre_index');
            $table->dropIndex('notes_lookup_semestre_index');
            $table->dropColumn('semestre');
        });
    }
};
