<?php

namespace App\Http\Controllers\Ajax\Payments;

use App\Enums\PaymentSystemEnum;
use App\Events\OrderCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Repositories\Contracts\OrderRepositoryContract;
use App\Services\Contracts\PaypalServiceContract;
use App\Services\PaypalService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaypalController extends Controller
{
    public function __construct(
        protected PaypalServiceContract   $paypalService,
        protected OrderRepositoryContract $orderRepository
    )
    {
    }

    public function create(CreateOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $paypalOrderId = $this->paypalService->create();

            if (! $paypalOrderId) {
                throw new \Exception('Could not create paypal order. Payment was not completed');
            }

            $data = [
                ...$request->validated(),
                'vendor_order_id' => $paypalOrderId
            ];

            $order = $this->orderRepository->create($data);

            DB::commit();

            return response()->json($order);
        } catch (\Throwable $exception) {
            DB::rollBack();

            logs()->error($exception);

            return response()->json([
               'error' => $exception->getMessage()
            ], 422);
        }
    }

    public function capture(string $vendorOrderId)
    {
        try {
            DB::beginTransaction();

            $paymentStatus = $this->paypalService->capture($vendorOrderId);

            $order = $this->orderRepository->setTransaction(
                $vendorOrderId,
                PaymentSystemEnum::Paypal,
                $paymentStatus
            );

            Cart::instance('cart')->destroy();

            OrderCreatedEvent::dispatch($order);

            DB::commit();

            return response()->json($order);
        } catch (\Throwable $exception) {
            DB::rollBack();

            logs()->error($exception);

            return response()->json([
                'error' => $exception->getMessage()
            ], 422);
        }
    }
}
