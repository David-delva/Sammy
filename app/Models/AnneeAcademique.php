<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class AnneeAcademique extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'active'];

    public static function getActive()
    {
        return self::where('active', true)->first();
    }

    /**
     * Build an academic year label for a given date.
     * Example: date in 2025-10 -> "2025-2026"; date in 2026-03 -> "2025-2026".
     */
    public static function labelForDate($date = null): string
    {
        $d = $date ? Carbon::parse($date) : Carbon::now();
        // Academic year starts in September: if month >= 9, start is this year, else previous year
        $startYear = $d->month >= 9 ? $d->year : $d->year - 1;
        $endYear = $startYear + 1;
        return sprintf('%d-%d', $startYear, $endYear);
    }

    /**
     * Return the academic year that contains the given date.
     * If $createIfMissing is true, create the record when missing.
     */
    public static function forDate($date = null, bool $createIfMissing = false): ?self
    {
        $label = self::labelForDate($date);
        $query = self::where('libelle', $label);
        $instance = $query->first();

        if (!$instance && $createIfMissing) {
            $instance = self::create(['libelle' => $label, 'active' => false]);
        }

        return $instance;
    }

    /**
     * Get the active academic year. If a date is provided, prefer the academic year
     * that contains that date (by label), otherwise fall back to the record flagged active.
     */
    public static function getActiveByDate($date = null): ?self
    {
        $byDate = self::forDate($date, false);
        if ($byDate) {
            return $byDate;
        }

        return self::where('active', true)->first();
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'classe_matiere')
            ->withPivot('matiere_id', 'coefficient')
            ->withTimestamps();
    }
}
