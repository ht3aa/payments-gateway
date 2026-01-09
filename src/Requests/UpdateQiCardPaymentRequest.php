<?php

namespace Ht3aa\PaymentsGateway\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQiCardPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'requestId' => 'required|string|exists:qi_card_payments,request_id',
            'paymentId' => 'required|string|exists:qi_card_payments,payment_id',
            'status' => 'required|string',
        ];
    }
}
