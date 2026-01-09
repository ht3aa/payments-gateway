<?php

namespace Ht3aa\PaymentsGateway\Repositores;

use Ht3aa\PaymentsGateway\Models\ZainCashTransaction;
use Ht3aa\PaymentsGateway\Services\ZainCashService;
use Ht3aa\PaymentsGateway\Enums\ZainCashStatus;

class ZainCashTransactionRepository
{
    public function __construct(
        private ZainCashService $zainCashService,
    ) {
        $this->zainCashService = $zainCashService;
    }

    public function createTransaction(string $orderId): ZainCashTransaction
    {
        $order = Order::find($orderId);

        $transaction = ZainCashTransaction::firstOrCreate([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
        ], [
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'amount' => $order->final_total,
            'service_type' => 'Jawaher Online Shop',
            'status' => ZainCashStatus::PENDING->value,
        ]);

        return $this->zainCashService->initiateTransaction($transaction);
    }

    public function showTransaction(ZainCashTransaction $transaction): ZainCashTransaction
    {
        return $this->zainCashService->checkTransaction($transaction);
    }

    public function updateTransaction(ZainCashTransaction $transaction, string $token): ZainCashTransaction
    {
        $tokenData = $this->zainCashService->decodeRedirectToken($token);

        $transaction->update([
            'status' => $tokenData->status,
        ]);

        return $transaction->fresh();
    }

    public function getByTransactionId(string $transactionId): ?ZainCashTransaction
    {
        return ZainCashTransaction::where('transaction_id', $transactionId)->first();
    }
}
