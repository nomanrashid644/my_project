<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'pharmacy_id', 'category_id', 'medicine_name', 'generic_name', 'strength',
    'dosage_form', 'price', 'stock_quantity', 'description',
    'requires_prescription', 'is_active', 'created_by', 'updated_by',
])]
#[Hidden(['created_at', 'updated_at', 'deleted_at'])]
class Medicine extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'requires_prescription' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['availability'];

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicineCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function alternatives(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'medicine_alternatives',
            'medicine_id',
            'alternative_medicine_id'
        )->withPivot('reason')->withTimestamps();
    }

    public function getAvailabilityAttribute(): string
    {
        return $this->stock_quantity > 0 ? 'Available' : 'Out of Stock';
    }

    #[Scope]
    protected function available(Builder $query): void
    {
        $query->where('stock_quantity', '>', 0);
    }
}