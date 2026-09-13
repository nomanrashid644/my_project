<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'is_active'])]
class MedicineCategory extends Model
{
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class, 'category_id');
    }
}