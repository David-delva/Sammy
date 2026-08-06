<?php

declare(strict_types=1);

namespace App\Support\Authorization;

enum Role: string
{
    case ADMIN = 'admin';
    case SECRETARIAT = 'secretariat';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases(),
        );
    }

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return self::tryFrom(strtolower(trim($value)));
    }
}
