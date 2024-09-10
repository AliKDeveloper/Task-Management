<?php

namespace App\Enums;

enum RoleNameEnum: string
{
    case PRODUCT_OWNER = 'Product Owner';
    case DEVELOPER = 'Developer';
    case TESTER = 'Tester';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
