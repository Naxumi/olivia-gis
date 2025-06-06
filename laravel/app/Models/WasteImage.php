<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class WasteImage extends Model
{
    use HasFactory;

    protected $fillable = ['waste_id', 'path'];

    /**
     * Tambahkan URL lengkap ke dalam serialisasi JSON.
     */
    protected $appends = ['url'];

    /**
     * Accessor untuk mendapatkan URL lengkap gambar.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::url($this->path),
        );
    }

    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }
}
