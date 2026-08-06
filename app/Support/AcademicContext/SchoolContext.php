<?php

declare(strict_types=1);

namespace App\Support\AcademicContext;

final class SchoolContext
{
    public function isSingleSchool(): bool
    {
        return true;
    }

    public function schoolKey(): string
    {
        return 'default-school';
    }
}
