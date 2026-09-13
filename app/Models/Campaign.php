<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'pharmacy_id', 'title', 'description', 'discount_type', 'discount_value',
    'promo_code', 'starts_at', 'ends_at', 'usage_limit', 'used_count',
    'is_active', 'created_by',
])]
class Campaign extends Model
{
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'campaign_medicines')->withTimestamps();
    }

    public function isCurrentlyValid(): bool
    {
        return $this->is_active && now()->between($this->starts_at, $this->ends_at)
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function discountFor(float $subtotal): float
    {
        $discount = $this->discount_type === 'percentage'
            ? $subtotal * ((float) $this->discount_value / 100)
            : (float) $this->discount_value;

        return min($subtotal, max(0, round($discount, 2)));
    }
}