<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ThankYouController extends Controller
{
    public function __invoke(string $vendorOrderId)
    {
        try {
            $order = Order::with(['transaction', 'products'])
                ->where('vendor_order_id', $vendorOrderId)
                ->firstOrFail();

            $showInvoiceBtn = auth()->check() && auth()->id() === $order?->user_id;

            return view('orders/thank-you', compact('order', 'showInvoiceBtn'));
        } catch (\Throwable $th) {
            logs()->warning($th);

            return redirect()->route('home');
        }
    }
}
