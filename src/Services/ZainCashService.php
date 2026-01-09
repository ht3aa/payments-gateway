<?php

namespace Ht3aa\PaymentsGateway\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ht3aa\PaymentsGateway\Models\ZainCashTransaction;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use stdClass;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ZainCashService
{
    private string $base_url;

    private string $merchant_secret;

    private string $merchant_id;

    private string $msisdn;

    private string $payment_redirect_url;

    private PendingRequest $client;

    public function __construct()
    {
        $this->base_url = config('payments-gateway.zaincash.is_production')
            ? 'https://api.zaincash.iq'
            : 'https://test.zaincash.iq';
        $this->payment_redirect_url = $this->base_url.'/transaction/pay?id=';
        $this->merchant_secret = config('payments-gateway.zaincash.merchant_secret');
        $this->merchant_id = config('payments-gateway.zaincash.merchant_id');
        $this->msisdn = config('payments-gateway.zaincash.msisdn');

        $this->client = Http::baseUrl($this->base_url);
    }

    public function initiateTransaction(ZainCashTransaction $transaction): ZainCashTransaction
    {
        $data = [
            'msisdn' => $this->msisdn,
            // 'amount' => $transaction->amount,
            'amount' => 1000,
            'serviceType' => $transaction->service_type,
            'orderId' => $transaction->order_id,
            'redirectUrl' => route('zain-cash-transaction.update', ['zain_cash_transaction' => $transaction->id]),
            'iat' => time(),
            'exp' => time() + 60 * 60 * 4,
        ];

        $data['token'] = urlencode(JWT::encode($data, $this->merchant_secret, 'HS256'));
        $data['merchantId'] = $this->merchant_id;

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);
        $response = json_decode(file_get_contents($this->base_url.'/transaction/init', false, $context), true);

        if ($this->responseFailed($response)) {
            Log::error('Failed to initiate transaction', $response);
            throw new UnprocessableEntityHttpException('Failed to initiate transaction');
        }

        $transaction->update([
            'iat' => $data['iat'],
            'exp' => $data['exp'],
            'token' => $data['token'],
            'redirect_url' => $data['redirectUrl'],
            'payment_redirect_url' => $this->payment_redirect_url.$response['id'],
            'zain_cash_response' => $response,
            'status' => $response['status'],
            'transaction_id' => $response['id'],
        ]);

        return $transaction->fresh();
    }

    public function checkTransaction(ZainCashTransaction $transaction): ZainCashTransaction
    {

        $data = [
            'id' => $transaction->transaction_id,
            'msisdn' => $this->msisdn,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 4,
        ];

        $data['token'] = urlencode(JWT::encode($data, $this->merchant_secret, 'HS256'));
        $data['merchantId'] = $this->merchant_id;

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $response = json_decode(file_get_contents($this->base_url.'/transaction/get', false, $context), true);

        if ($this->responseFailed($response)) {
            Log::error('Failed to initiate transaction', $response);
            throw new UnprocessableEntityHttpException('Failed to initiate transaction');
        }

        $transaction->update([
            'payment_response' => $response,
            'status' => $response['status'],
        ]);

        return $transaction->fresh();
    }

    public function decodeRedirectToken(string $token): stdClass
    {
        return JWT::decode($token, new Key($this->merchant_secret, 'HS256'));
    }

    private function responseFailed($response): bool
    {
        return isset($response['err']);
    }
}
