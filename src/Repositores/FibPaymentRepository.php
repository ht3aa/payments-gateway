<?php

namespace Ht3aa\PaymentsGateway\Repositores;

use Ht3aa\PaymentsGateway\Enums\FibPaymentStatus;
use Ht3aa\PaymentsGateway\Models\FibPayment;
use Ht3aa\PaymentsGateway\Services\FibService;

class FibPaymentRepository
{
    public function __construct(
        private FibService $fibService,
    ) {
        $this->fibService = $fibService;
    }

    public function createPayment(string $orderId): FibPayment
    {
        $order = Order::find($orderId);

        $fibPayment = FibPayment::create([
            'order_id' => $orderId,
            'customer_id' => $order->customer_id,
            'amount' => $order->final_total,
            'currency' => strtoupper($order->currency->code),
            'status' => FibPaymentStatus::PENDING->value,
        ]);

        return $this->fibService->createPayment($fibPayment->fresh());
    }

    public function showPayment(FibPayment $fibPayment): FibPayment
    {
        return $this->fibService->getPayment($fibPayment);
    }

    public function getByPaymentId(string $paymentId): ?FibPayment
    {
        return FibPayment::where('payment_id', $paymentId)->first();
    }
}
