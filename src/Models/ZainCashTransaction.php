<?php

namespace Ht3aa\PaymentsGateway\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ZainCashTransaction extends Model
{
    use LogsActivity;

    protected $fillable = [
        'amount',
        'service_type',
        'order_id',
        'customer_id',
        'redirect_url',
        'token',
        'iat',
        'exp',
        'zain_cash_response',
        'status',
        'payment_redirect_url',
        'transaction_id',
        'payment_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'iat' => 'datetime',
        'exp' => 'datetime',
        'zain_cash_response' => 'array',
        'payment_response' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

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
