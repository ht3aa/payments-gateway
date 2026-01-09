<?php

namespace Ht3aa\PaymentsGateway\Controllers;

use Ht3aa\PaymentsGateway\Models\FibPayment;
use Ht3aa\PaymentsGateway\Repositores\FibPaymentRepository;
use Ht3aa\PaymentsGateway\Requests\StoreFibPaymentRequest;
use Ht3aa\PaymentsGateway\Requests\UpdateFibPaymentRequest;
use Ht3aa\PaymentsGateway\Resources\FibPaymentResource;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FibPaymentController extends Controller
{
    public function __construct(
        private FibPaymentRepository $fibPaymentRepository,
    ) {
        $this->fibPaymentRepository = $fibPaymentRepository;
    }

    public function store(StoreFibPaymentRequest $request): FibPaymentResource
    {
        $data = $request->validated();

        $fibPayment = $this->fibPaymentRepository->createPayment($data['order_id']);

        return new FibPaymentResource($fibPayment);
    }

    public function show(FibPayment $fibPayment): FibPaymentResource
    {
        return new FibPaymentResource($this->fibPaymentRepository->showPayment($fibPayment));
    }

    public function update(UpdateFibPaymentRequest $request, FibPayment $fibPayment)
    {
        $data = $request->validated();

        $fibPayment = $this->fibPaymentRepository->getByPaymentId($data['id']);

        if (! $fibPayment) {
            throw new NotFoundHttpException('Fib payment not found');
        }

        $fibPayment = $this->fibPaymentRepository->showPayment($fibPayment);

        return new FibPaymentResource($fibPayment);
    }
}
