<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classe_matiere', function (Blueprint $table) {
            $table->unsignedTinyInteger('credits')->default(0)->after('coefficient');
        });
    }

    public function down(): void
    {
        Schema::table('classe_matiere', function (Blueprint $table) {
            $table->dropColumn('credits');
        });
    }
};
