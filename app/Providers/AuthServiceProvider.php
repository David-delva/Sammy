<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Facture;
use App\Models\Matiere;
use App\Models\Note;
use App\Policies\AnneeAcademiquePolicy;
use App\Policies\ClassePolicy;
use App\Policies\ElevePolicy;
use App\Policies\FacturePolicy;
use App\Policies\MatierePolicy;
use App\Policies\NotePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        AnneeAcademique::class => AnneeAcademiquePolicy::class,
        Classe::class => ClassePolicy::class,
        Eleve::class => ElevePolicy::class,
        Facture::class => FacturePolicy::class,
        Matiere::class => MatierePolicy::class,
        Note::class => NotePolicy::class,
    ];

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
