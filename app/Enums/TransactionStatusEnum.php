<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case Success = 'success';
    case Cancelled = 'cancelled';
    case Pending = 'pending';
}
