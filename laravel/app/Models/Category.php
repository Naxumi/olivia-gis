<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['name'];

    /**
     * Fasilitas yang menerima kategori limbah ini.
     */
    public function recyclingFacilities(): BelongsToMany
    {
        return $this->belongsToMany(RecyclingFacility::class, 'category_recycling_facility');
    }
}
