<?php

namespace Ht3aa\PaymentsGateway\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SwitchCheckoutResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'checkout_id' => $this->checkout_id,
            'integrity' => $this->integrity,
            'status' => $this->status->value,
        ];
    }
}
