<?php

namespace App\Enum;

enum RoleEnum: string
{
    case USER = 'ROLE_USER';
    case FRIEND = 'ROLE_FRIEND';
    case MODERATOR = 'ROLE_MODERATOR';
    case ADMIN = 'ROLE_ADMIN';

    // Role extérieur à l'api
    case AUTHOR = 'ROLE_AUTHOR'; // Blog

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Utilisateur',
            self::FRIEND => 'Ami',
            self::MODERATOR => 'Modérateur',
            self::ADMIN => 'Administrateur',
            self::AUTHOR => 'Auteur',
        };
    }
}
