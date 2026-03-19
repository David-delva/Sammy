<?php

namespace Tests\Unit;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\CalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private CalculationService $service;
    private AnneeAcademique $annee;
    private Classe $classe;
    private Eleve $eleve;
    private Matiere $matiere;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(CalculationService::class);

        $this->annee  = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active'  => true,
        ]);

        $this->classe = Classe::create(['nom_classe' => 'Test A']);

        $this->matiere = Matiere::create([
            'nom_matiere' => 'Mathématiques',
            'coefficient' => 4,
            'classe_id'   => $this->classe->id,
        ]);

        $this->eleve = Eleve::create([
            'matricule'      => 'TST-001',
            'nom'            => 'Dupont',
            'prenom'         => 'Jean',
            'date_naissance' => '2010-01-01',
            'sexe'           => 'M',
        ]);

        Inscription::create([
            'eleve_id'            => $this->eleve->id,
            'classe_id'           => $this->classe->id,
            'annee_academique_id' => $this->annee->id,
        ]);
    }

    public function test_get_mention(): void
    {
        $this->assertSame('Excellent', $this->service->getMention(16.5));
        $this->assertSame('Très Bien', $this->service->getMention(14.5));
        $this->assertSame('Bien', $this->service->getMention(12.5));
        $this->assertSame('Assez Bien', $this->service->getMention(10.5));
        $this->assertSame('Insuffisant', $this->service->getMention(8.5));
    }

    public function test_moyenne_matiere_avec_devoir_et_composition(): void
    {
        Note::create([
            'eleve_id'            => $this->eleve->id,
            'matiere_id'          => $this->matiere->id,
            'annee_academique_id' => $this->annee->id,
            'note'                => 14.00,
            'type_devoir'         => 'devoir',
        ]);
        Note::create([
            'eleve_id'            => $this->eleve->id,
            'matiere_id'          => $this->matiere->id,
            'annee_academique_id' => $this->annee->id,
            'note'                => 16.00,
            'type_devoir'         => 'composition',
        ]);

        // (14 + 16) / 2 = 15
        $moyenne = $this->service->calculateMoyenneMatiere($this->eleve, $this->matiere, $this->annee);
        $this->assertEquals(15.0, $moyenne);
    }

    public function test_moyenne_generale_calcule_correctement(): void
    {
        $matiere2 = Matiere::create([
            'nom_matiere' => 'Français',
            'coefficient' => 2,
            'classe_id'   => $this->classe->id,
        ]);

        // Maths (coeff 4): Moyenne 15
        Note::create(['eleve_id' => $this->eleve->id, 'matiere_id' => $this->matiere->id, 'annee_academique_id' => $this->annee->id, 'note' => 15, 'type_devoir' => 'devoir']);
        Note::create(['eleve_id' => $this->eleve->id, 'matiere_id' => $this->matiere->id, 'annee_academique_id' => $this->annee->id, 'note' => 15, 'type_devoir' => 'composition']);

        // Français (coeff 2): Moyenne 12
        Note::create(['eleve_id' => $this->eleve->id, 'matiere_id' => $matiere2->id, 'annee_academique_id' => $this->annee->id, 'note' => 12, 'type_devoir' => 'devoir']);
        Note::create(['eleve_id' => $this->eleve->id, 'matiere_id' => $matiere2->id, 'annee_academique_id' => $this->annee->id, 'note' => 12, 'type_devoir' => 'composition']);

        // MG = (15*4 + 12*2) / (4+2) = (60 + 24) / 6 = 84 / 6 = 14
        $mg = $this->service->calculateMoyenneGenerale($this->eleve, $this->annee);
        $this->assertEquals(14.0, $mg);
    }
}
