<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_year_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_academique_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['annee_academique_id', 'user_id'], 'academic_year_user_permissions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_year_user_permissions');
    }
};
