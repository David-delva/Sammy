<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Authorization\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Test accounts that should bypass manual email verification.
     *
     * @var list<string>
     */
    protected const AUTO_VERIFIED_EMAILS = [
        'admin@ecole',
        'admin@ecole.com',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function shouldAutoVerifyEmail(): bool
    {
        return in_array(strtolower((string) $this->email), self::AUTO_VERIFIED_EMAILS, true);
    }

    public function ensureAutoVerified(): void
    {
        if (! $this->shouldAutoVerifyEmail() || $this->hasVerifiedEmail()) {
            return;
        }

        $this->forceFill([
            'email_verified_at' => now(),
        ])->save();
    }

    public function hasRole(Role|string $role): bool
    {
        $expected = $role instanceof Role
            ? $role->value
            : strtolower(trim($role));

        return strtolower((string) $this->role) === $expected;
    }

    /**
     * @param  array<int, Role|string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function isSecretariat(): bool
    {
        return $this->hasRole(Role::SECRETARIAT);
    }
}
