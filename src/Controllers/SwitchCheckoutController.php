<?php

namespace Ht3aa\PaymentsGateway\Controllers;

use Ht3aa\PaymentsGateway\Repositores\SwitchCheckoutRepository;
use Ht3aa\PaymentsGateway\Requests\StoreSwitchCheckoutRequest;
use Ht3aa\PaymentsGateway\Requests\UpdateSwitchCheckoutRequest;
use Ht3aa\PaymentsGateway\Resources\SwitchCheckoutResource;
use Ht3aa\PaymentsGateway\Models\SwitchCheckout;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SwitchCheckoutController extends Controller
{
    public function __construct(
        private SwitchCheckoutRepository $switchCheckoutRepository,
    ) {
        $this->switchCheckoutRepository = $switchCheckoutRepository;
    }

    public function store(StoreSwitchCheckoutRequest $request): SwitchCheckoutResource
    {
        $data = $request->validated();

        $switchCheckout = $this->switchCheckoutRepository->checkout($data['order_id']);

        return new SwitchCheckoutResource($switchCheckout);
    }

    public function update(UpdateSwitchCheckoutRequest $request, SwitchCheckout $switchCheckout)
    {
        $data = $request->validated();
        $switchCheckout = $this->switchCheckoutRepository->getByCheckoutId($switchCheckout->checkout_id);

        if (! $switchCheckout) {
            throw new NotFoundHttpException('Switch checkout not found');
        }

        $switchCheckout = $this->switchCheckoutRepository->update($switchCheckout, $data['resourcePath']);

        return new SwitchCheckoutResource($switchCheckout);
    }
}
