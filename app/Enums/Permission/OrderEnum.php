<?php

namespace App\Enums\Permission;

enum OrderEnum: string
{
    case EDIT = 'edit order';
    case DELETE = 'delete order';

    public static function values(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }
}