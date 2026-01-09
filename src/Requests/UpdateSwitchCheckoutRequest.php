<?php

namespace Ht3aa\PaymentsGateway\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSwitchCheckoutRequest extends FormRequest
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
            'id' => 'required|string|exists:switch_checkouts,checkout_id',
            'resourcePath' => 'required|string',
        ];
    }
}
