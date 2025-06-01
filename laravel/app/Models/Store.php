<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point dari Magellan

class Store extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'location', // Kolom PostGIS Anda
    ];
    public $timestamps = true; // Asumsikan Anda ingin created_at & updated_at

    protected $casts = [
        'location' => Point::class, // Casting ke objek Point dari Magellan
    ];

    public function user(): BelongsTo // Seller
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wastes(): HasMany
    {
        return $this->hasMany(Waste::class);
    }
}
