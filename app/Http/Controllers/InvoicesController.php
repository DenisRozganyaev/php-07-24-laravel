<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Contracts\InvoicesServiceContract;

class InvoicesController extends Controller
{
    public function __invoke(Order $order, InvoicesServiceContract $invoicesService)
    {
        $this->authorize('view', $order);

        return $invoicesService->generate($order)->stream();
    }
}
