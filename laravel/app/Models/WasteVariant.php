<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteVariant extends Model
{
    use HasFactory;
    protected $fillable = ['waste_id', 'volume_in_grams', 'price', 'stock'];
    public function waste(): BelongsTo
    {
        return $this->belongsTo(Waste::class);
    }
}
