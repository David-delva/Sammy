<?php

use App\Models\Facture;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('numero')->unique();
            $table->string('libelle', 150);
            $table->text('description')->nullable();
            $table->decimal('montant', 12, 2);
            $table->enum('statut', [
                Facture::STATUT_EMISE,
                Facture::STATUT_PARTIELLEMENT_PAYEE,
                Facture::STATUT_PAYEE,
                Facture::STATUT_ANNULEE,
            ])->default(Facture::STATUT_EMISE);
            $table->date('date_emission');
            $table->date('date_echeance')->nullable();
            $table->date('date_paiement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
