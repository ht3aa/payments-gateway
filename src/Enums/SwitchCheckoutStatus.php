<?php

namespace Ht3aa\PaymentsGateway\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SwitchCheckoutStatus: string implements HasColor, HasLabel
{
    /**
     * The pending checkout status.
     *
     * @var string
     */
    case PENDING = 'pending';

    /**
     * The success checkout status.
     *
     * @var string
     */
    case SUCCESS = 'success';

    /**
     * The failed checkout status.
     *
     * @var string
     */
    case FAILED = 'failed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('switchCheckout.form.status.pending'),
            self::SUCCESS => __('switchCheckout.form.status.success'),
            self::FAILED => __('switchCheckout.form.status.failed'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
        };
    }
}
