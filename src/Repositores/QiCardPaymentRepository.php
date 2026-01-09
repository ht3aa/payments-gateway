<?php

namespace Ht3aa\PaymentsGateway\Repositores;

use Ht3aa\PaymentsGateway\Models\QiCardPayment;
use Ht3aa\PaymentsGateway\Services\QiCardService;
use Ht3aa\PaymentsGateway\Enums\QiCardPaymentStatus;
use Illuminate\Support\Str;

class QiCardPaymentRepository
{
    public function __construct(
        private QiCardService $qiCardService,
    ) {
        $this->qiCardService = $qiCardService;
    }

    public function createPayment(string $orderId): QiCardPayment
    {
        $order = Order::find($orderId);

        $qiCardPayment = QiCardPayment::create([
            'order_id' => $orderId,
            'customer_id' => $order->customer_id,
            'amount' => $order->final_total,
            'currency' => strtoupper($order->currency->code),
            'status' => QiCardPaymentStatus::CREATED->value,
            'request_id' => Str::uuid()->toString(),
        ]);

        return $this->qiCardService->createPayment($qiCardPayment->fresh());
    }

    public function updatePayment(QiCardPayment $qiCardPayment, array $data): QiCardPayment
    {
        $qiCardPayment->update([
            'status' => $data['status'],
        ]);

        return $qiCardPayment->fresh();
    }

    public function showPayment(QiCardPayment $qiCardPayment): QiCardPayment
    {
        return $this->qiCardService->getPayment($qiCardPayment);
    }

    public function getByPaymentId(string $paymentId): ?QiCardPayment
    {
        return QiCardPayment::where('payment_id', $paymentId)->first();
    }

    public function getByOrderId(string $orderId): ?QiCardPayment
    {
        return QiCardPayment::where('order_id', $orderId)
            ->latest()
            ->first();
    }
}
