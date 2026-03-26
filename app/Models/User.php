<?php

namespace App\Models;

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
}
