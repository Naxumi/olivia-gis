<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'address', 'latitude', 'longitude'];
    public $timestamps = true; // Asumsikan Anda ingin created_at & updated_at

    public function user(): BelongsTo // Seller
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wastes(): HasMany
    {
        return $this->hasMany(Waste::class);
    }
}
