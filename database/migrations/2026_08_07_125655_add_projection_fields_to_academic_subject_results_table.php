<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_subject_results', function (Blueprint $table) {
            $table->decimal('note_rattrapage', 5, 2)->nullable()->after('note_composition');
            $table->boolean('rattrapage_utilise')->default(false)->after('note_rattrapage');
            $table->unsignedTinyInteger('credits_acquis')->default(0)->after('coefficient');
            $table->unsignedSmallInteger('heures_absence')->default(0)->after('credits_acquis');
        });
    }

    public function down(): void
    {
        Schema::table('academic_subject_results', function (Blueprint $table) {
            $table->dropColumn(['note_rattrapage', 'rattrapage_utilise', 'credits_acquis', 'heures_absence']);
        });
    }
};
