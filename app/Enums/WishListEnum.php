<?php

namespace App\Enums;

use BackedEnum;

enum WishListEnum: string
{
    case Price = 'price';
    case In_Stock = 'in_stock';
}
