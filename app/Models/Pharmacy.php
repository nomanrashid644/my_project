<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'slug', 'address', 'city', 'contact_number', 'email',
    'latitude', 'longitude', 'is_active',
])]
#[Hidden(['created_at', 'updated_at'])]
class Pharmacy extends Model
{
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(PharmacyRating::class);
    }
}