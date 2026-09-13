<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id', 'pharmacy_id', 'order_number', 'status', 'subtotal', 'discount_amount',
    'delivery_fee', 'total_amount', 'payment_method', 'payment_status',
    'delivery_address', 'delivery_phone', 'customer_notes', 'confirmed_at',
    'dispatched_at', 'delivered_at', 'cancelled_at',
])]
class Order extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'preparing', 'dispatched', 'delivered', 'cancelled'];

    public const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['dispatched'],
        'dispatched' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2',
            'delivery_fee' => 'decimal:2', 'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime', 'dispatched_at' => 'datetime',
            'delivered_at' => 'datetime', 'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(PharmacyRating::class);
    }

    public function deliveryAssignment(): HasOne
    {
        return $this->hasOne(DeliveryAssignment::class);
    }
}