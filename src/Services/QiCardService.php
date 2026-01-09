<?php

namespace Ht3aa\PaymentsGateway\Services;

use Ht3aa\PaymentsGateway\Models\QiCardPayment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

// Qi Card Payment Gateway Service
class QiCardService
{
    private string $baseUrl;

    private string $terminalId;

    private string $username;

    private string $password;

    private ?string $token = null;

    private PendingRequest $client;

    public function __construct()
    {
        $this->baseUrl = config('payments-gateway.qi_card.api_url');
        $this->terminalId = config('payments-gateway.qi_card.terminal_id');
        $this->username = config('payments-gateway.qi_card.username');
        $this->password = config('payments-gateway.qi_card.password');

        $this->client = Http::baseUrl($this->baseUrl)
            ->withBasicAuth($this->username, $this->password)
            ->withHeaders([
                'X-Terminal-Id' => $this->terminalId,
            ]);
    }

    public function createPayment(QiCardPayment $qiCardPayment): QiCardPayment
    {
        $data = [
            'requestId' => $qiCardPayment->request_id,
            'amount' => $qiCardPayment->amount,
            'currency' => $qiCardPayment->currency,
            'finishPaymentUrl' => route('home'),
            'notificationUrl' => route('qi-card-payment.post.update', $qiCardPayment->id),
        ];

        $response = $this->client->withToken($this->token)
            ->asJson()
            ->post('/payment', $data);

        if ($response->failed()) {
            Log::error('QiCard: Failed to create payment', $response->json());
            throw new BadRequestException('Failed to create Qi Card payment');
        }

        $result = $response->json();

        $qiCardPayment->update([
            'payment_id' => $result['paymentId'],
            'form_url' => $result['formUrl'],
            'status' => $result['status'],
            'finish_payment_url' => $data['finishPaymentUrl'],
            'notification_url' => $data['notificationUrl'],
            'creation_date' => $result['creationDate'],
        ]);

        return $qiCardPayment->fresh();
    }

    public function getPayment(QiCardPayment $qiCardPayment): QiCardPayment
    {
        $response = $this->client
            ->asJson()
            ->get("/payment/{$qiCardPayment->payment_id}/status");

        if ($response->failed()) {
            Log::error('QiCard: Failed to get payment status', [
                'response' => $response->json(),
                'payment_id' => $qiCardPayment->payment_id,
            ]);
            throw new BadRequestException('Failed to get Qi Card payment status');
        }

        $result = $response->json();

        $qiCardPayment->update([
            'status' => $result['status'],
            'canceled' => $result['canceled'],
            'update_response_data' => $result,
        ]);

        return $qiCardPayment->fresh();
    }

    public function refundPayment(QiCardPayment $qiCardPayment, float $amount): QiCardPayment
    {
        $response = $this->client->withToken($this->token)
            ->asJson()
            ->post("/payment/{$qiCardPayment->payment_id}/refund", [
                'requestId' => Str::uuid()->toString(),
                'amount' => $amount,
                'message' => 'Refund',
            ]);

        if ($response->failed()) {
            Log::error('QiCard: Failed to refund payment', [
                'response' => $response->json(),
                'payment_id' => $qiCardPayment->payment_id,
            ]);
            throw new BadRequestException('Failed to refund Qi Card payment');
        }

        $result = $response->json();

        if ($result['status'] === 'SUCCESS') {
            $qiCardPayment->update([
                'status' => 'REFUNDED',
                'refund_response_data' => $result,
            ]);
        }

        return $this->getPayment($qiCardPayment);
    }
}
