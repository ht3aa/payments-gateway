<?php

namespace Ht3aa\PaymentsGateway\Controllers;

use Ht3aa\PaymentsGateway\Repositores\ZainCashTransactionRepository;
use Ht3aa\PaymentsGateway\Requests\StoreZainCashTransactionRequest;
use Ht3aa\PaymentsGateway\Requests\UpdateZainCashTransactionRequest;
use Ht3aa\PaymentsGateway\Resources\ZainCashTransactionResource;
use Ht3aa\PaymentsGateway\Models\ZainCashTransaction;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class ZainCashTransactionController extends Controller
{
    public function __construct(
        private ZainCashTransactionRepository $zainCashTransactionRepository,
    ) {
        $this->zainCashTransactionRepository = $zainCashTransactionRepository;
    }

    public function store(StoreZainCashTransactionRequest $request): ZainCashTransactionResource
    {
        $data = $request->validated();

        $transaction = $this->zainCashTransactionRepository->createTransaction($data['order_id']);

        return new ZainCashTransactionResource($transaction);
    }

    public function show(ZainCashTransaction $zainCashTransaction): ZainCashTransactionResource
    {
        return new ZainCashTransactionResource($this->zainCashTransactionRepository->showTransaction($zainCashTransaction));
    }

    public function update(UpdateZainCashTransactionRequest $request, ZainCashTransaction $zainCashTransaction)
    {
        $data = $request->validated();

        $transaction = $this->zainCashTransactionRepository->updateTransaction($zainCashTransaction, $data['token']);

        return new ZainCashTransactionResource($transaction);
    }
}
