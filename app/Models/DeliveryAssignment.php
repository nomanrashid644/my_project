<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'rider_id', 'status', 'assigned_at', 'picked_up_at', 'delivered_at', 'delivery_notes', 'current_latitude', 'current_longitude', 'last_location_at'])]
class DeliveryAssignment extends Model
{
    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'picked_up_at' => 'datetime', 'delivered_at' => 'datetime', 'last_location_at' => 'datetime', 'current_latitude' => 'decimal:7', 'current_longitude' => 'decimal:7'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function rider(): BelongsTo { return $this->belongsTo(User::class, 'rider_id'); }
}