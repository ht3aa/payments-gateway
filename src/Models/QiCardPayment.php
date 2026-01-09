<?php

namespace Ht3aa\PaymentsGateway\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class QiCardPayment extends Model
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
        'request_id',
        'finish_payment_url',
        'notification_url',
        'canceled',
        'fees',
        'customer_info',
        'browser_info',
        'additional_info',
        'payment_id',
        'creation_date',
        'form_url',
        'update_response_data',
        'refund_response_data',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'canceled' => 'boolean',
        'fees' => 'decimal:2',
        'customer_info' => 'array',
        'browser_info' => 'array',
        'creation_date' => 'datetime',
        'additional_info' => 'array',
        'update_response_data' => 'array',
        'refund_response_data' => 'array',
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

    /**
     * Get the order associated with the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer associated with the payment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
