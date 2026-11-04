<?php

namespace App\Models;

use App\Enums\Role;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    /**
     * Determine if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * Determine if the user has the standard user role.
     */
    public function isUser(): bool
    {
        return $this->role === Role::User;
    }

    /**
     * Determine if the user has the guest role.
     */
    public function isGuest(): bool
    {
        return $this->role === Role::Guest;
    }
}
