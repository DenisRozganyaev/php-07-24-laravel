<?php

namespace App\Repositories;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentSystemEnum;
use App\Enums\TransactionStatusEnum;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;

class OrderRepository implements Contracts\OrderRepositoryContract
{
    public function create(array $data): Order|false
    {
        $data = array_merge($data, [
            'total' => Cart::instance('cart')->total(),
            'status' => OrderStatusEnum::InProcess,
        ]);

        $order = auth()->check()
            ? auth()->user()->orders()->create($data)
            : Order::create($data);

        $this->addProductsToOrder($order);

        return $order;
    }

    public function setTransaction(string $vendorOrderId, PaymentSystemEnum $paymentSystem, TransactionStatusEnum $status): Order
    {
        $order = Order::query()->where('vendor_order_id', $vendorOrderId)->firstOrFail();

        $order->transaction()->create([
            'payment_system' => $paymentSystem,
            'status' => $status,
        ]);

        $order->update([
            'status' => match ($status) {
                TransactionStatusEnum::Success => OrderStatusEnum::Paid,
                TransactionStatusEnum::Cancelled => OrderStatusEnum::Cancelled,
                default => OrderStatusEnum::InProcess,
            },
        ]);

        return $order;
    }

    protected function addProductsToOrder(Order $order): void
    {
        Cart::instance('cart')->content()->each(function ($item) use ($order) {
            $product = $item->model;

            $order->products()->attach($product, [
                'quantity' => $item->qty,
                'single_price' => $item->price,
                'name' => $product->title,
                'attributes' => $item->options->implode(function ($opt, $key) {
                    return "$key: $opt";
                }),
            ]);

            $quantity = $product->quantity - $item->qty;

            if ($quantity < 0 || ! $product->update(['quantity' => $quantity])) {
                throw new \Exception("Not enough product [$product->name] quantity");
            }
        });
    }
}
