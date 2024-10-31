<?php

namespace App\Services;

use App\Enums\TransactionStatusEnum;
use App\Services\Contracts\PaypalServiceContract;
use Gloudemans\Shoppingcart\Facades\Cart;
use Srmklive\PayPal\Services\PayPal;

class PaypalService implements Contracts\PaypalServiceContract
{

    protected Paypal $paypal;

    public function __construct()
    {
        $this->paypal = app(Paypal::class);
        $this->paypal->setApiCredentials(config('paypal'));
        $this->paypal->setAccessToken($this->paypal->getAccessToken());
    }

    public function create(): ?string
    {
        $paypalOrder = $this->paypal->createOrder(
            $this->buildOrderRequestData()
        );

        logs()->info('Paypal create order response:', [
            'response' => $paypalOrder
        ]);

        return $paypalOrder['id'] ?? null;
    }

    public function capture(string $vendorOrderId): TransactionStatusEnum
    {
        $result = $this->paypal->capturePaymentOrder($vendorOrderId);

        logs()->info('Paypal capture order response:', [
            'response' => $result
        ]);

        return match($result['status']) {
            'COMPLETED', 'APPROVED' => TransactionStatusEnum::Success,
            'CREATED', 'SAVED' => TransactionStatusEnum::Pending,
            default => TransactionStatusEnum::Cancelled
        };
    }

    protected function buildOrderRequestData(): array
    {
        $cart = Cart::instance('cart');
        $currencyCode = config('paypal.currency');
        $items = [];

        $cart->content()
            ->each(function ($cartItem) use (&$items, $currencyCode) {
                $items[] = [
                    'name' => $cartItem->name,
                    'quantity' => $cartItem->qty,
                    'sku' => $cartItem->model->SKU,
                    'url' => url(route('products.show', $cartItem->model)),
                    'category' => 'PHYSICAL_GOODS',
                    'unit_amount' => [
                        'value' => $cartItem->price,
                        'currency_code' => $currencyCode,
                    ],
                    'tax' => [
                        'value' => round($cartItem->price / 100 * $cartItem->taxRate, 2),
                        'currency_code' => $currencyCode,
                    ],
                ];
            });

        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currencyCode,
                        'value' => $cart->total(),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => $currencyCode,
                                'value' => $cart->subtotal(),
                            ],
                            'tax_total' => [
                                'currency_code' => $currencyCode,
                                'value' => $cart->tax(),
                            ],
                        ],
                    ],
                    'items' => $items,
                ],
            ],
        ];
    }
}
