<?php

namespace Ht3aa\PaymentsGateway\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ht3aa\PaymentsGateway\PaymentsGateway
 */
class PaymentsGateway extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ht3aa\PaymentsGateway\PaymentsGateway::class;
    }
}
