<?php

namespace Ht3aa\PaymentsGateway\Services;

use Ht3aa\PaymentsGateway\Models\FibPayment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

// Fib Payment Gateway Service
class FibService
{
    private string $baseUrl;

    private string $clientId;

    private string $clientSecret;

    private ?string $token = null;

    private int $expiresIn;

    private PendingRequest $client;

    public function __construct()
    {
        $this->baseUrl = config('payments-gateway.fib.is_production')
            ? config('payments-gateway.fib.production_base_url')
            : config('payments-gateway.fib.test_base_url');
        $this->clientId = config('payments-gateway.fib.client_id');
        $this->clientSecret = config('payments-gateway.fib.client_secret');

        $this->client = Http::baseUrl($this->baseUrl);
    }

    public function login(): void
    {
        $response = $this->client->asForm()->post("/auth/realms/fib-online-shop/protocol/openid-connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        if ($response->failed()) {
            Log::error('Fib: Failed to login', $response->json());
            throw new BadRequestException('Failed to login');
        }

        $data = $response->json();

        $this->token = $data['access_token'];
        $this->expiresIn = $data['expires_in'];
    }

    public function createPayment(FibPayment $fibPayment): FibPayment
    {
        $this->login();


        $data = [
            "monetaryValue" => [
                'amount' => $fibPayment->amount,
                'currency' => $fibPayment->currency,
            ],
            "statusCallbackUrl" => route('fib-payment.update', $fibPayment->id),
            "category" => "ECOMMERCE",
        ];

        $response = $this->client->withToken($this->token)
            ->asJson()
            ->post("/protected/v1/payments", $data);

        if ($response->failed()) {
            Log::error('Fib: Failed to create payment', $response->json());
            throw new BadRequestException('Failed to create payment');
        }

        $result = $response->json();


        $fibPayment->update([
            'status_callback_url' => route('fib-payment.update', $fibPayment->id),
            'payment_id' => $result['paymentId'],
            'readable_code' => $result['readableCode'],
            'personal_app_link' => $result['personalAppLink'],
            'business_app_link' => $result['businessAppLink'],
            'corporate_app_link' => $result['corporateAppLink'],
            'valid_until' => $result['validUntil'],
            'qr_code' => $result['qrCode'],
        ]);

        return $fibPayment->fresh();
    }

    public function getPayment(FibPayment $fibPayment): FibPayment
    {
        $this->login();

        $response = $this->client->withToken($this->token)
            ->asJson()
            ->get("protected/v1/payments/{$fibPayment->payment_id}/status");

        if ($response->failed()) {
            Log::error('Fib: Failed to get payment status', $response->json());
            throw new BadRequestException('Failed to get payment status');
        }

        $result = $response->json();

        $fibPayment->update([
            'status' => $result['status'],
            'paid_at' => $result['paidAt'],
            'declining_reason' => $result['decliningReason'],
            'declined_at' => $result['declinedAt'],
            'paid_by' => $result['paidBy'],
        ]);

        return $fibPayment->fresh();
    }

    public function refundPayment(FibPayment $fibPayment): FibPayment
    {
        $this->login();

        $response = $this->client->withToken($this->token)
            ->asJson()
            ->post("protected/v1/payments/{$fibPayment->payment_id}/refund");

        if ($response->failed()) {
            Log::error('Fib: Failed to refund payment', $response->json());
            throw new BadRequestException('Failed to refund payment');
        }

        return $this->getPayment($fibPayment);
    }

    private function transactionSuccess(string $code): bool
    {
        // TODO: Update with actual Fib API success codes
        return preg_match('/^(000.000.|000.100.1|000.[36]|000.400.[1][12]0)/', $code);
    }

    private function transactionFailed(string $code): bool
    {
        return ! $this->transactionSuccess($code);
    }

    private function transactionStillPending(string $code): bool
    {
        // TODO: Update with actual Fib API pending code
        return $code === '000.200.000';
    }
}
