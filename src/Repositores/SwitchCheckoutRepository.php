<?php

namespace Ht3aa\PaymentsGateway\Repositores;

use Ht3aa\PaymentsGateway\Enums\SwitchCheckoutStatus;
use Ht3aa\PaymentsGateway\Models\SwitchCheckout;
use Ht3aa\PaymentsGateway\Services\SwitchService;

class SwitchCheckoutRepository
{
    public function __construct(
        private SwitchService $switchService,
    ) {
        $this->switchService = $switchService;
    }

    public function checkout(string $orderId): SwitchCheckout
    {
        $order = Order::find($orderId);

        if ($switchCheckout = $this->customerAlreadyHasPendingCheckout($orderId, $order->customer_id)) {
            return $switchCheckout;
        }

        $switchCheckout = SwitchCheckout::make([
            'order_id' => $orderId,
            'customer_id' => $order->customer_id,
            'amount' => $order->final_total,
            'currency' => strtoupper($order->currency->code),
            'payment_type' => 'DB', // default according to the api documentation
            'integrity' => true,
            'status' => SwitchCheckoutStatus::PENDING,
        ]);

        $switchCheckout = $this->switchService->prepareCheckout($switchCheckout);

        $switchCheckout->save();

        return $switchCheckout;
    }

    public function update(SwitchCheckout $switchCheckout, string $resourcePath): SwitchCheckout
    {
        return $this->switchService->updateCheckout($switchCheckout, $resourcePath);
    }

    public function getByCheckoutId(string $checkoutId): ?SwitchCheckout
    {
        return SwitchCheckout::where('checkout_id', $checkoutId)->first();
    }

    private function customerAlreadyHasPendingCheckout(string $orderId, int $customerId): ?SwitchCheckout
    {
        return SwitchCheckout::where('order_id', $orderId)->where('customer_id', $customerId)->where('status', SwitchCheckoutStatus::PENDING)->first();
    }
}
