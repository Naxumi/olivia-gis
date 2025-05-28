<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan jika belum ada dan Anda menggunakan factory
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logistics extends Model
{
    use HasFactory; // Tambahkan jika belum ada

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'distributor_id',
        'status',
        'distance_km',              // Opsional, jika diisi saat create
        'duration_minutes',         // Opsional
        'estimated_delivery_time',  // Opsional
        'current_latitude',         // Opsional
        'current_longitude',        // Opsional
        // 'last_updated_at' biasanya dihandle otomatis jika $timestamps = true
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distance_km' => 'decimal:2',
        'current_latitude' => 'decimal:6',
        'current_longitude' => 'decimal:6',
        'estimated_delivery_time' => 'datetime',
        // last_updated_at akan otomatis di-cast jika timestamps aktif
    ];

    /**
     * Mendapatkan transaksi yang terkait dengan logistik ini.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Mendapatkan user distributor yang terkait dengan logistik ini.
     * Beri nama relasi yang jelas, misal distributorUser atau hanya distributor.
     */
    public function distributorUser(): BelongsTo // Atau public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    // Jika Anda tidak menggunakan kolom updated_at default dari Eloquent,
    // dan hanya memiliki last_updated_at seperti di DDL, Anda mungkin perlu
    // mengatur timestamps. DDL Anda memiliki:
    // last_updated_at timestamp(0) without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP
    // created_at TIDAK ADA di DDL Logistics.
    //
    // Jika Anda ingin Eloquent mengelola created_at dan updated_at:
    // public $timestamps = true;
    //
    // Jika DDL Anda TIDAK memiliki created_at dan updated_at, tapi punya last_updated_at:
    // public $timestamps = false; // Matikan default created_at dan updated_at Eloquent
    // Dan Anda perlu mengupdate last_updated_at secara manual atau melalui event/observer
    // Namun, DDL Anda untuk logistics.last_updated_at sudah memiliki DEFAULT CURRENT_TIMESTAMP,
    // jadi untuk pembuatan record baru, DB akan mengisinya jika tidak Anda berikan.
    // Untuk update, Anda perlu mengisinya manual jika $timestamps = false.

    // Jika Anda ingin Eloquent secara otomatis mengelola kolom 'created_at' dan 'last_updated_at'
    // sebagai kolom timestamp, Anda bisa mendefinisikannya:
    // const CREATED_AT = 'created_at'; // jika Anda menambahkan kolom ini di DDL
    // const UPDATED_AT = 'last_updated_at';
    // Namun, DDL Anda tidak memiliki `created_at` untuk logistics.
    // Jadi, paling sederhana adalah $timestamps = false dan handle `last_updated_at` jika diperlukan saat update.
    // Atau, tambahkan `created_at` ke DDL jika ingin Eloquent mengelolanya.

    // Untuk saat ini, dengan DDL yang ada:
    public $timestamps = false; // Karena tidak ada kolom 'created_at' & 'updated_at' standar
    // `last_updated_at` memiliki default di DB untuk pembuatan.
    // Untuk update `last_updated_at`, Anda perlu melakukannya manual.
    // Di controller Anda sudah ada: $transaction->logistics->update(['status' => 'in_transit', 'last_updated_at' => Carbon::now()]);
    // ini sudah benar jika $timestamps = false.
}
