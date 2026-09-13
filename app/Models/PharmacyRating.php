<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'pharmacy_id', 'order_id', 'rating', 'comment', 'is_approved'])]
class PharmacyRating extends Model
{
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function pharmacy(): BelongsTo { return $this->belongsTo(Pharmacy::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}