<?php

use Ht3aa\PaymentsGateway\Controllers\FibPaymentController;
use Ht3aa\PaymentsGateway\Controllers\PaymentMethodController;
use Ht3aa\PaymentsGateway\Controllers\QiCardPaymentController;
use Ht3aa\PaymentsGateway\Controllers\SwitchCheckoutController;
use Ht3aa\PaymentsGateway\Controllers\ZainCashTransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments-gateway')->group(function () {

    Route::apiResource('switch-checkouts', SwitchCheckoutController::class)->only(['store', 'update']);
    Route::apiResource('fib-payments', FibPaymentController::class)->only(['store', 'update', 'show']);
    Route::apiResource('qi-card-payments', QiCardPaymentController::class)->only(['store', 'update', 'show']);
    Route::apiResource('zain-cash-transactions', ZainCashTransactionController::class)->only(['store', 'show']);

    // for webhook purposes 
    Route::get('zain-cash-transactions/update/{zain_cash_transaction}', [ZainCashTransactionController::class, 'update'])->name('zain-cash-transaction.update');

    // for webhook purposes
    Route::post('qi-card-payments/update/{qi_card_payment}', [QiCardPaymentController::class, 'update'])->name('qi-card-payment.post.update');
});
