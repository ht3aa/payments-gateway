<?php

namespace Ht3aa\PaymentsGateway;

use Ht3aa\PaymentsGateway\Commands\PaymentsGatewayCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PaymentsGatewayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('payments-gateway')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_payments_gateway_table')
            ->hasCommand(PaymentsGatewayCommand::class);
    }
}
