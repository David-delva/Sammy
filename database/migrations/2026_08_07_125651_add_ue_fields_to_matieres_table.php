<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->foreignId('ue_id')->nullable()->after('nom_matiere')->constrained('ues')->nullOnDelete();
            $table->unsignedTinyInteger('coefficient')->nullable()->after('ue_id');
            $table->unsignedTinyInteger('credits')->nullable()->after('coefficient');
        });
    }

    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ue_id');
            $table->dropColumn(['coefficient', 'credits']);
        });
    }
};
