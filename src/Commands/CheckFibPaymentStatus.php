<?php

namespace Ht3aa\PaymentsGateway\Commands;

use Ht3aa\PaymentsGateway\Models\FibPayment;
use Ht3aa\PaymentsGateway\Services\FibService;
use Ht3aa\PaymentsGateway\Enums\FibPaymentStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CheckFibPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fib:check-payment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check FIB payment status for pending and refund requested payments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking FIB payment statuses...');

        // Get the first pending or refund requested payment
        $payment = FibPayment::whereIn('status', [
            FibPaymentStatus::PENDING,
            FibPaymentStatus::REFUND_REQUESTED,
        ])
            ->whereNotNull('payment_id')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$payment) {
            $this->info('No pending or refund requested payments found.');
            return self::SUCCESS;
        }

        $this->info("Checking payment ID: {$payment->payment_id}");

        try {
            $fibService = new FibService();
            $updatedPayment = $fibService->getPayment($payment);

            $this->info("Payment status updated: {$updatedPayment->status->value}");
            Log::info('FIB Payment status checked', [
                'payment_id' => $payment->payment_id,
                'old_status' => $payment->status->value,
                'new_status' => $updatedPayment->status->value,
            ]);

            return self::SUCCESS;
        } catch (BadRequestException $e) {
            $this->error("Failed to check payment status: {$e->getMessage()}");
            Log::error('FIB Payment status check failed', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
