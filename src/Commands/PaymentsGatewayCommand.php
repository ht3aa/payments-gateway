<?php

namespace Ht3aa\PaymentsGateway\Commands;

use Illuminate\Console\Command;

class PaymentsGatewayCommand extends Command
{
    public $signature = 'payments-gateway';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
