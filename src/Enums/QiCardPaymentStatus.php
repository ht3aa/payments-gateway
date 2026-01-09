<?php

namespace Ht3aa\PaymentsGateway\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QiCardPaymentStatus: string implements HasColor, HasLabel
{
    /**
     * The pending payment status.
     *
     * @var string
     */
    case CREATED = 'CREATED';

    /**
     * The paid payment status.
     *
     * @var string
     */
    case SUCCESS = 'SUCCESS';

    /**
     * The unpaid payment status.
     *
     * @var string
     */
    case FAILED = 'FAILED';

    /**
     * The declined payment status.
     *
     * @var string
     */
    case CANCELLED = 'CANCELLED';

    /**
     * The refunded payment status.
     *
     * @var string
     */
    case REFUNDED = 'REFUNDED';

    case FORM_SHOWED = 'FORM_SHOWED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CREATED => __('qiCardPayment.form.status.created'),
            self::SUCCESS => __('qiCardPayment.form.status.success'),
            self::FAILED => __('qiCardPayment.form.status.failed'),
            self::CANCELLED => __('qiCardPayment.form.status.cancelled'),
            self::REFUNDED => __('qiCardPayment.form.status.refunded'),
            self::FORM_SHOWED => __('qiCardPayment.form.status.form_showed'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CREATED => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'gray',
            self::REFUNDED => 'info',
            self::FORM_SHOWED => 'primary',
        };
    }
}
