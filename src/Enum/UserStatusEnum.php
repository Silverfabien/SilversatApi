<?php

namespace App\Enum;

enum UserStatusEnum: string
{
    case ACTIVE = 'ACTIVE';
    case SOFT_DELETED = 'SOFT_DELETED';
    case DELETED = 'DELETED';
    case BLOCKED = 'BLOCKED';
    case BANNED = 'BANNED';
}
