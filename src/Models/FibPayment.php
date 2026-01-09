<?php

namespace Ht3aa\PaymentsGateway\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FibPayment extends Model
{
    use LogsActivity, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'customer_id',
        'amount',
        'currency',
        'status_callback_url',
        'description',
        'redirect_url',
        'expires_in',
        'category',
        'refundable_for',
        'payment_id',
        'readable_code',
        'personal_app_link',
        'business_app_link',
        'corporate_app_link',
        'valid_until',
        'qr_code',
        'status',
        'paid_at',
        'declining_reason',
        'declined_at',
        'paid_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'paid_at' => 'datetime',
        'declined_at' => 'datetime',
        'paid_by' => 'array',
    ];

    /**
     * Get the options for the log.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
