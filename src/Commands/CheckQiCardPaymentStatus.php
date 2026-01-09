<?php

namespace Ht3aa\PaymentsGateway\Commands;

use Ht3aa\PaymentsGateway\Enums\QiCardPaymentStatus;
use Ht3aa\PaymentsGateway\Models\QiCardPayment;
use Ht3aa\PaymentsGateway\Services\QiCardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CheckQiCardPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qi-card:check-payment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Qi Card payment status for created payments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking Qi Card payment statuses...');

        // Get the first created payment
        $payment = QiCardPayment::whereIn('status', [QiCardPaymentStatus::CREATED, QiCardPaymentStatus::FORM_SHOWED])
            ->whereNotNull('payment_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (! $payment) {
            $this->info('No pending Qi Card payments found.');

            return self::SUCCESS;
        }

        $this->info("Checking payment ID: {$payment->payment_id}");

        try {
            $qiCardService = new QiCardService;
            $updatedPayment = $qiCardService->getPayment($payment);

            $this->info("Payment status updated: {$updatedPayment->status}");
            Log::info('Qi Card Payment status checked', [
                'payment_id' => $payment->payment_id,
                'old_status' => $payment->status,
                'new_status' => $updatedPayment->status,
            ]);

            return self::SUCCESS;
        } catch (BadRequestException $e) {
            $this->error("Failed to check payment status: {$e->getMessage()}");
            Log::error('Qi Card Payment status check failed', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
