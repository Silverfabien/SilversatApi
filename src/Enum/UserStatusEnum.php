<?php

namespace App\Enum;

enum UserStatusEnum: string
{
    case ACTIVE = 'ACTIVE';
    case DELETED = 'DELETED';
    case BLOCKED = 'BLOCKED';
    case BANNED = 'BANNED';
}
