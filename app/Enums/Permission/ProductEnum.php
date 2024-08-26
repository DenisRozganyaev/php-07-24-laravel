<?php

namespace App\Enums\Permission;

enum ProductEnum: string
{
    case PUBLISH = 'publish product';
    case EDIT = 'edit product';
    case DELETE = 'delete product';

    public static function values(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }
}
