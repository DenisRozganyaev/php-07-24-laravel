<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice;

class InvoicesService implements Contracts\InvoicesServiceContract
{

    public function generate(Order $order): Invoice
    {
        $order->loadMissing(['transaction', 'products']);

        $customer = new Buyer([
            'name' => $order->name . ' ' . $order->lastname,
            'phone' => $order->phone,
            'custom_fields' => [
                'email' => $order->email,
                'city' => $order->city,
                'address' => $order->address
            ]
        ]);

        $invoice = Invoice::make('receipt')
            ->status($order->status->value)
            ->buyer($customer)
            ->filename($order->vendor_order_id)
            ->taxRate(config('cart.tax'))
            ->addItems($this->invoiceItems($order->products))
            ->logo(public_path('vendor/invoices/sample-logo.png'))
            ->save('public');

        if ($order->status->value === OrderStatusEnum::InProcess->value) {
            $invoice->payUntilDays(config('invoices.date.pay_until_days'));
        }

        return $invoice;
    }

    protected function invoiceItems(Collection $products): array
    {
        $items = [];

        foreach ($products as $product) {
            $items[] = InvoiceItem::make($product->title)
                ->description($product->pivot->attributes)
                ->pricePerUnit($product->pivot->single_price)
                ->quantity($product->pivot->quantity)
                ->units('шт');
        }

        return $items;
    }
}
