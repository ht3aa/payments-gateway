<?php

namespace Ht3aa\PaymentsGateway\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FibPaymentStatus: string implements HasColor, HasLabel
{
    /**
     * The pending payment status.
     *
     * @var string
     */
    case PENDING = 'PENDING';

    /**
     * The paid payment status.
     *
     * @var string
     */
    case PAID = 'PAID';

    /**
     * The unpaid payment status.
     *
     * @var string
     */
    case UNPAID = 'UNPAID';

    /**
     * The declined payment status.
     *
     * @var string
     */
    case DECLINED = 'DECLINED';

    /**
     * The refunded payment status.
     *
     * @var string
     */
    case REFUND_REQUESTED = 'REFUND_REQUESTED';

    /**
     * The refunded payment status.
     *
     * @var string
     */
    case REFUNDED = 'REFUNDED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('fibPayment.form.status.pending'),
            self::PAID => __('fibPayment.form.status.paid'),
            self::UNPAID => __('fibPayment.form.status.unpaid'),
            self::DECLINED => __('fibPayment.form.status.declined'),
            self::REFUND_REQUESTED => __('fibPayment.form.status.refund_requested'),
            self::REFUNDED => __('fibPayment.form.status.refunded'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::UNPAID => 'gray',
            self::DECLINED => 'danger',
            self::REFUND_REQUESTED => 'info',
            self::REFUNDED => 'success',
        };
    }
}
