<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case User = 'user';
    case Guest = 'guest';

    /**
     * Get a human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::User => 'User',
            self::Guest => 'Guest',
        };
    }
}
