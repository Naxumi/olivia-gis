<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point dari Magellan
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class RecyclingFacility extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'name',
        'type',
        'address',
        'location', // Kolom PostGIS
        'operational_hours',
        'contact_person',
        'contact_phone',
        'owner_id',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     */
    protected $casts = [
        'location' => Point::class, // Menggunakan caster dari Magellan
    ];

    protected $appends = ['image_url'];

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->image_path
                ? Storage::url($this->image_path)
                : asset('images/default-facility.png'), // Sediakan gambar default
        );
    }

    /**
     * Relasi ke User (pemilik fasilitas).
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Kategori limbah yang diterima oleh fasilitas ini.
     */
    public function acceptedCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_recycling_facility');
    }
}
