<?php

namespace Ht3aa\PaymentsGateway\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ZainCashStatus: string implements HasColor, HasLabel
{
    /**
     * The pending transaction status.
     *
     * @var string
     */
    case PENDING = 'pending';

    /**
     * The success transaction status (alias for completed).
     *
     * @var string
     */
    case SUCCESS = 'success';

    /**
     * The failed transaction status.
     *
     * @var string
     */
    case FAILED = 'failed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('zainCashTransaction.form.status.pending'),
            self::SUCCESS => __('zainCashTransaction.form.status.success'),
            self::FAILED => __('zainCashTransaction.form.status.failed'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
        };
    }
}
