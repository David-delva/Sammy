<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AcademicWriteAccessService
{
    public function __construct(
        private readonly AcademicYearService $academicYears,
    ) {}

    public function canManageSelectedYear(?User $user): bool
    {
        return $this->canManageYear($user, $this->academicYears->forDate());
    }

    public function canManageYear(?User $user, ?AnneeAcademique $annee): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'secretariat' || ! $annee) {
            return false;
        }

        if ($this->isCurrentCalendarYear($annee)) {
            return true;
        }

        if (! Schema::hasTable('academic_year_user_permissions')) {
            return false;
        }

        return DB::table('academic_year_user_permissions')
            ->where('annee_academique_id', $annee->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function isCurrentCalendarYear(AnneeAcademique $annee): bool
    {
        return $annee->libelle === AnneeAcademique::labelForDate(now()->toDateString());
    }

    public function denialMessage(): string
    {
        return "Le secretariat ne peut modifier que l'annee academique en cours, sauf autorisation explicite d'un administrateur.";
    }
}
