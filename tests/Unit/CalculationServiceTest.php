<?php

namespace Tests\Unit;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\AcademicCacheService;
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

        $this->annee = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active' => true,
        ]);

        $this->classe = Classe::create(['nom_classe' => 'Test A']);

        $this->matiere = Matiere::create([
            'nom_matiere' => 'Mathematiques',
        ]);

        $this->classe->matieres()->attach($this->matiere->id, [
            'annee_academique_id' => $this->annee->id,
            'coefficient' => 4,
        ]);

        $this->eleve = Eleve::create([
            'matricule' => 'TST-001',
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'date_naissance' => '2010-01-01',
            'sexe' => 'M',
        ]);

        Inscription::create([
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'annee_academique_id' => $this->annee->id,
        ]);

        session(['academic_year_date' => '2025-10-01']);
    }

    public function test_get_mention(): void
    {
        $this->assertSame('Excellent', $this->service->getMention(16.5));
        $this->assertSame('Tres Bien', $this->service->getMention(14.5));
        $this->assertSame('Bien', $this->service->getMention(12.5));
        $this->assertSame('Assez Bien', $this->service->getMention(10.5));
        $this->assertSame('Insuffisant', $this->service->getMention(8.5));
    }

    public function test_moyenne_matiere_peut_etre_calculee_par_semestre(): void
    {
        $this->createNote($this->matiere, Note::SEMESTRE_1, 'devoir', 14.0);
        $this->createNote($this->matiere, Note::SEMESTRE_1, 'composition', 16.0);
        $this->createNote($this->matiere, Note::SEMESTRE_2, 'devoir', 10.0);
        $this->createNote($this->matiere, Note::SEMESTRE_2, 'composition', 12.0);

        $this->assertEquals(15.0, $this->service->calculateMoyenneMatiere($this->eleve, $this->matiere, $this->annee, Note::SEMESTRE_1));
        $this->assertEquals(11.0, $this->service->calculateMoyenneMatiere($this->eleve, $this->matiere, $this->annee, Note::SEMESTRE_2));
    }

    public function test_moyenne_generale_calcule_correctement_pour_un_semestre(): void
    {
        $matiere2 = Matiere::create(['nom_matiere' => 'Francais']);
        $this->classe->matieres()->attach($matiere2->id, [
            'annee_academique_id' => $this->annee->id,
            'coefficient' => 2,
        ]);

        $this->createNote($this->matiere, Note::SEMESTRE_1, 'devoir', 15.0);
        $this->createNote($this->matiere, Note::SEMESTRE_1, 'composition', 15.0);
        $this->createNote($matiere2, Note::SEMESTRE_1, 'devoir', 12.0);
        $this->createNote($matiere2, Note::SEMESTRE_1, 'composition', 12.0);

        $this->assertEquals(14.0, $this->service->calculateMoyenneGenerale($this->eleve, $this->annee, Note::SEMESTRE_1));
    }

    public function test_get_bulletin_data_retourne_les_moyennes_semestrielles(): void
    {
        $matiere2 = Matiere::create(['nom_matiere' => 'Francais']);
        $this->classe->matieres()->attach($matiere2->id, [
            'annee_academique_id' => $this->annee->id,
            'coefficient' => 2,
        ]);

        $this->createNote($this->matiere, Note::SEMESTRE_1, 'devoir', 15.0);
        $this->createNote($this->matiere, Note::SEMESTRE_1, 'composition', 15.0);
        $this->createNote($matiere2, Note::SEMESTRE_1, 'devoir', 12.0);
        $this->createNote($matiere2, Note::SEMESTRE_1, 'composition', 12.0);

        $this->createNote($this->matiere, Note::SEMESTRE_2, 'devoir', 13.0);
        $this->createNote($this->matiere, Note::SEMESTRE_2, 'composition', 13.0);
        $this->createNote($matiere2, Note::SEMESTRE_2, 'devoir', 14.0);
        $this->createNote($matiere2, Note::SEMESTRE_2, 'composition', 14.0);

        $bulletin = $this->service->getBulletinData($this->eleve, Note::SEMESTRE_1);

        $this->assertSame('BULLETIN DU 1° SEMESTRE', $bulletin['bulletin_titre']);
        $this->assertEquals(14.0, $bulletin['moyenne_semestre_1']);
        $this->assertEquals(13.33, $bulletin['moyenne_semestre_2']);
        $this->assertEquals(13.67, $bulletin['moyenne_annuelle']);
        $this->assertCount(2, $bulletin['lignes']);
        $this->assertSame('15,00', $bulletin['lignes'][0]['moyenne']);
    }

    public function test_moyenne_matiere_est_recalculee_quand_la_version_du_cache_academique_change(): void
    {
        $this->createNote($this->matiere, Note::SEMESTRE_1, 'devoir', 14.0);
        $this->createNote($this->matiere, Note::SEMESTRE_1, 'composition', 16.0);

        $this->assertEquals(15.0, $this->service->calculateMoyenneMatiere($this->eleve, $this->matiere, $this->annee, Note::SEMESTRE_1));

        Note::query()
            ->where('eleve_id', $this->eleve->id)
            ->where('matiere_id', $this->matiere->id)
            ->where('annee_academique_id', $this->annee->id)
            ->where('type_devoir', 'composition')
            ->where('semestre', Note::SEMESTRE_1)
            ->update(['note' => 20.0]);

        $this->assertEquals(15.0, $this->service->calculateMoyenneMatiere($this->eleve, $this->matiere, $this->annee, Note::SEMESTRE_1));

        app(AcademicCacheService::class)->bust();

        $this->assertEquals(17.0, $this->service->calculateMoyenneMatiere($this->eleve, $this->matiere, $this->annee, Note::SEMESTRE_1));
    }

    private function createNote(Matiere $matiere, int $semestre, string $type, float $note): void
    {
        Note::create([
            'eleve_id' => $this->eleve->id,
            'matiere_id' => $matiere->id,
            'annee_academique_id' => $this->annee->id,
            'note' => $note,
            'type_devoir' => $type,
            'semestre' => $semestre,
        ]);
    }
}