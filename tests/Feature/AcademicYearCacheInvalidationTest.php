<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\User;
use App\Services\AcademicCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AcademicYearCacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_academic_year_mutations_bust_scoped_cache_without_flushing_unrelated_entries(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $academicCache = app(AcademicCacheService::class);
        $initialVersion = $academicCache->version();
        $unrelatedCacheKey = 'unrelated:cache:' . uniqid('', true);

        Cache::forever($unrelatedCacheKey, 'keep-me');

        $this->actingAs($user)
            ->post(route('annees.store'), [
                'libelle' => '2025-2026',
                'active' => true,
            ])
            ->assertRedirect(route('annees.index'));

        $this->assertSame($initialVersion + 1, $academicCache->version());
        $this->assertSame('keep-me', Cache::get($unrelatedCacheKey));

        $annee = AnneeAcademique::query()->firstOrFail();

        $this->actingAs($user)
            ->put(route('annees.update', $annee), [
                'libelle' => '2026-2027',
                'active' => true,
            ])
            ->assertRedirect(route('annees.index'));

        $this->assertSame($initialVersion + 2, $academicCache->version());
        $this->assertSame('keep-me', Cache::get($unrelatedCacheKey));

        $this->actingAs($user)
            ->delete(route('annees.destroy', $annee))
            ->assertRedirect(route('annees.index'));

        $this->assertSame($initialVersion + 3, $academicCache->version());
        $this->assertSame('keep-me', Cache::get($unrelatedCacheKey));
    }
}