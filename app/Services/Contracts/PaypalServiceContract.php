<?php

namespace App\Services\Contracts;

use App\Enums\TransactionStatusEnum;

interface PaypalServiceContract
{
    public function create(): ?string;
    public function capture(string $vendorOrderId): TransactionStatusEnum;
}
