<?php

namespace Ht3aa\PaymentsGateway\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FibPaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        if (request()->routeIs('fib-payment.show')) {
            return $this->getPaymentShowData();
        }

        return parent::toArray($request);
    }

    private function getPaymentShowData(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'paid_at' => $this->paid_at,
            'declined_at' => $this->declined_at,
        ];
    }
}
