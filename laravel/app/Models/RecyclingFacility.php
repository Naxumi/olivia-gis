<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point dari Magellan

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

    /**
     * Relasi ke User (pemilik fasilitas).
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}