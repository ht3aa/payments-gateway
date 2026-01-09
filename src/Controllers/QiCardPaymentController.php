<?php

namespace Ht3aa\PaymentsGateway\Controllers;

use Ht3aa\PaymentsGateway\Repositores\QiCardPaymentRepository;
use Ht3aa\PaymentsGateway\Requests\StoreQiCardPaymentRequest;
use Ht3aa\PaymentsGateway\Requests\UpdateQiCardPaymentRequest;
use Ht3aa\PaymentsGateway\Resources\QiCardPaymentResource;
use Ht3aa\PaymentsGateway\Models\QiCardPayment;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class QiCardPaymentController extends Controller
{
    public function __construct(
        private QiCardPaymentRepository $qiCardPaymentRepository,
    ) {
        $this->qiCardPaymentRepository = $qiCardPaymentRepository;
    }

    public function store(StoreQiCardPaymentRequest $request): QiCardPaymentResource
    {
        $data = $request->validated();

        $qiCardPayment = $this->qiCardPaymentRepository->createPayment($data['order_id']);

        return new QiCardPaymentResource($qiCardPayment);
    }

    public function show(QiCardPayment $qiCardPayment): QiCardPaymentResource
    {
        return new QiCardPaymentResource($this->qiCardPaymentRepository->showPayment($qiCardPayment));
    }

    public function update(Request $request, QiCardPayment $qiCardPayment)
    {
        $data = $request->validated();

        $qiCardPayment = $this->qiCardPaymentRepository->getByPaymentId($data['paymentId']);

        if (! $qiCardPayment) {
            throw new NotFoundHttpException('Qi Card payment not found');
        }

        $qiCardPayment = $this->qiCardPaymentRepository->updatePayment($qiCardPayment, $data);

        return new QiCardPaymentResource($qiCardPayment);
    }
}
