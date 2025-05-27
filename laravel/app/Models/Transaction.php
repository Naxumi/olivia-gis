<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany; // Ditambahkan untuk ecoPointActivityLogs

class Transaction extends Model
{
    use HasFactory;

    // Konstanta untuk status transaksi
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seller_id',
        'buyer_id',
        'waste_variant_id',
        'store_id',
        'logistics_id', // Nullable
        'quantity',
        'total_price',
        'status',
        'eco_points_earned',
        'payment_method',
        'completed_at', // Nullable
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'completed_at' => 'datetime',
        'eco_points_earned' => 'integer',
        'quantity' => 'integer',
    ];

    /**
     * Relasi ke User (Pembeli).
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Relasi ke User (Penjual).
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Relasi ke WasteVariant.
     */
    public function wasteVariant(): BelongsTo
    {
        return $this->belongsTo(WasteVariant::class, 'waste_variant_id');
    }

    /**
     * Relasi ke Store.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Relasi ke Logistics (jika ada).
     */
    public function logistics(): BelongsTo
    {
        return $this->belongsTo(Logistics::class, 'logistics_id');
    }

    /**
     * Relasi ke Certificate (jika transaksi menghasilkan sertifikat).
     */
    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class, 'transaction_id');
    }

    /**
     * Relasi ke EcoPointLog yang terkait dengan transaksi ini.
     * Ini mengambil log poin di mana transaksi ini adalah referensinya.
     */
    public function ecoPointActivityLogs(): HasMany
    {
        // Mengasumsikan EcoPointLog memiliki konstanta SOURCE_TRANSACTION
        // Jika EcoPointLog::SOURCE_TRANSACTION belum didefinisikan, ganti 'transaction' dengan string literal.
        return $this->hasMany(EcoPointLog::class, 'reference_id')
            ->where('source_type', EcoPointLog::SOURCE_TRANSACTION ?? 'transaction');
    }
}
