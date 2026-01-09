<?php

namespace Ht3aa\PaymentsGateway\Services;

use Ht3aa\PaymentsGateway\Models\SwitchCheckout;
use Ht3aa\PaymentsGateway\Enums\SwitchCheckoutStatus;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

// https://hyperpay.docs.oppwa.com/integrations/widget (Switch Payment api)
class SwitchService
{
    private string $baseUrl;

    private string $resourcePathBaseUrl;

    private string $token;

    private string $entityId;

    private PendingRequest $client;

    public function __construct()
    {
        $this->baseUrl = config('services.switch.base_url');
        $this->resourcePathBaseUrl = config('services.switch.resource_path_base_url');
        $this->token = config('services.switch.token');
        $this->entityId = config('services.switch.entity_id');

        $this->client = Http::withToken($this->token)->asForm();
    }

    public function prepareCheckout(SwitchCheckout $switchCheckout): SwitchCheckout
    {
        $data = [
            'entityId' => $this->entityId,
            'amount' => number_format($switchCheckout->amount, 2),
            // 'amount' => 1,
            'currency' => $switchCheckout->currency,
            'paymentType' => $switchCheckout->payment_type ?? 'DB',
            'integrity' => 'true',
        ];

        $response = $this->client->post("{$this->baseUrl}/checkouts", $data);

        if ($response->failed()) {
            Log::error('Switch: Failed to create checkout', $response->json());
            throw new BadRequestException('Failed to create checkout');
        }

        $result = $response->json();

        $switchCheckout->checkout_id = $result['id'];
        $switchCheckout->checkout_data = $result;
        $switchCheckout->integrity = $result['integrity'];

        return $switchCheckout;
    }

    public function updateCheckout(SwitchCheckout $switchCheckout, string $resourcePath): SwitchCheckout
    {
        $response = Http::get("{$this->resourcePathBaseUrl}{$resourcePath}");

        if ($response->failed()) {
            Log::error('Switch: Failed to get checkout status', $response->json());
            throw new BadRequestException('Failed to get checkout status');
        }

        $result = $response->json();

        if ($this->transactionStillPending($result['result']['code'])) {
            Log::warning('Switch: Transaction still pending', $result);
            throw new UnprocessableEntityHttpException('Transaction still pending');
        }

        $switchCheckout->update([
            'status' => $this->transactionFailed($response->json()['result']['code']) ? SwitchCheckoutStatus::FAILED : SwitchCheckoutStatus::SUCCESS,
            'resource_path' => $resourcePath,
            'checkout_payment_data' => $result,
        ]);

        return $switchCheckout->fresh();
    }

    private function transactionSuccess(string $code): bool
    {
        return preg_match('/^(000.000.|000.100.1|000.[36]|000.400.[1][12]0)/', $code);
    }

    private function transactionFailed(string $code): bool
    {
        return ! $this->transactionSuccess($code);
    }

    private function transactionStillPending(string $code): bool
    {
        return $code === '000.200.000';
    }
}
