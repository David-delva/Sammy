<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $eleves = Eleve::all();

        foreach ($eleves as $eleve) {
            $matieres = Matiere::where('classe_id', $eleve->classe_id)->get();

            foreach ($matieres as $matiere) {
                Note::create([
                    'eleve_id' => $eleve->id,
                    'matiere_id' => $matiere->id,
                    'note' => rand(8, 20),
                    'type_devoir' => 'devoir',
                ]);

                Note::create([
                    'eleve_id' => $eleve->id,
                    'matiere_id' => $matiere->id,
                    'note' => rand(8, 20),
                    'type_devoir' => 'devoir',
                ]);

                Note::create([
                    'eleve_id' => $eleve->id,
                    'matiere_id' => $matiere->id,
                    'note' => rand(8, 20),
                    'type_devoir' => 'composition',
                ]);
            }
        }
    }
}
