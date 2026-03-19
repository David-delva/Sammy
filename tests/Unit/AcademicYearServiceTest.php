<?php

namespace Tests\Unit;

use App\Models\AnneeAcademique;
use App\Services\AcademicYearService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearServiceTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYearService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AcademicYearService::class);
    }

    public function test_label_for_date_septembre(): void
    {
        $label = AnneeAcademique::labelForDate('2025-09-01');
        $this->assertSame('2025-2026', $label);
    }

    public function test_label_for_date_janvier(): void
    {
        $label = AnneeAcademique::labelForDate('2026-01-15');
        $this->assertSame('2025-2026', $label);
    }

    public function test_for_date_retourne_annee_correspondante(): void
    {
        AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);
        $annee = $this->service->forDate('2025-10-01');

        $this->assertNotNull($annee);
        $this->assertSame('2025-2026', $annee->libelle);
    }
}
