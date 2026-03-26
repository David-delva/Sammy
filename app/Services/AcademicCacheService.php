<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class AcademicCacheService
{
    private const VERSION_KEY = 'academic_cache_version';

    public function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    public function scopedKey(string $key): string
    {
        return 'academic:v'.$this->version().':'.$key;
    }

    public function remember(string $key, int $ttl, Closure $callback): mixed
    {
        return Cache::remember($this->scopedKey($key), $ttl, $callback);
    }

    public function forget(string $key): void
    {
        Cache::forget($this->scopedKey($key));
    }

    public function bust(): int
    {
        $nextVersion = $this->version() + 1;

        Cache::forever(self::VERSION_KEY, $nextVersion);

        return $nextVersion;
    }

    public function dashboardStatsKey(?string $dateParam, ?int $academicYearId): string
    {
        return $dateParam
            ? "dashboard_stats:date:{$dateParam}"
            : 'dashboard_stats:annee:'.($academicYearId ?? 'global');
    }

    public function noteAverageKey(int $eleveId, int $matiereId, int $anneeId, ?int $semestre = null): string
    {
        return "moyenne:eleve:{$eleveId}:matiere:{$matiereId}:annee:{$anneeId}".$this->cacheSuffix($semestre);
    }

    private function cacheSuffix(?int $semestre): string
    {
        return $semestre === null ? ':annuel' : ':semestre:'.$semestre;
    }
}
