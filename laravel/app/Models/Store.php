<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point dari Magellan
use Illuminate\Database\Eloquent\Casts\Attribute; // Untuk accessor
use Illuminate\Support\Facades\Storage; // Untuk URL gambar

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
        'image_path', // <-- Tambahkan ini
    ];
    public $timestamps = true; // Asumsikan Anda ingin created_at & updated_at

    protected $casts = [
        'location' => Point::class, // Casting ke objek Point dari Magellan
    ];

    /**
     * Tambahkan URL gambar ke dalam serialisasi JSON.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Accessor untuk mendapatkan URL lengkap gambar toko.
     * Contoh penggunaan: $store->image_url
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->image_path
                ? Storage::url($this->image_path)
                : asset('images/default-store.png'), // Sediakan gambar default
        );
    }


    public function user(): BelongsTo // Seller
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wastes(): HasMany
    {
        return $this->hasMany(Waste::class);
    }
}
