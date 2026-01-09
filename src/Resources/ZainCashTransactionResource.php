<?php

namespace Ht3aa\PaymentsGateway\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ZainCashTransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        if (request()->routeIs('zain-cash-transaction.show')) {
            return $this->getTransactionShowData();
        }

        if (request()->routeIs('zain-cash-transaction.update')) {
            return $this->getTransactionUpdateData();
        }

        return parent::toArray($request);
    }

    private function getTransactionShowData(): array
    {
        return [
            'payment_response' => $this->payment_response,
        ];
    }

    private function getTransactionUpdateData(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
        ];
    }
}
