<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Waste extends Model
{
    use HasFactory;
    protected $fillable = ['store_id', 'category_id', 'name', 'stock', 'status', 'price', 'description', 'sold_count'];
    // Asumsikan price & stock di sini adalah default atau agregat
    // status ENUM: ['available', 'sold', 'expired']

    /**
     * Relasi ke gambar-gambar limbah.
     */
    public function images(): HasMany
    {
        return $this->hasMany(WasteImage::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class); // Relasi ke Store, asumsikan Waste dimiliki oleh Store
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function wasteVariants(): HasMany
    {
        return $this->hasMany(WasteVariant::class);
    }
    
    // Alias agar bisa dipanggil $waste->variants() selain $waste->wasteVariants()
    public function variants()
    {
        return $this->wasteVariants();
    }
}
