<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo; // Untuk relasi polymorphic

class EcoPointLog extends Model
{
    use HasFactory;

    /**
     * Konstanta untuk nilai yang mungkin di kolom 'source_type'.
     * Ini membantu menjaga konsistensi dan menghindari string literal di kode Anda.
     */
    public const SOURCE_TRANSACTION = 'transaction';
    public const SOURCE_REVIEW = 'review';
    public const SOURCE_REFERRAL = 'referral';
    // Tambahkan konstanta lain jika ada source_type baru

    /**
     * Konstanta untuk nilai yang mungkin di kolom 'points_type'.
     */
    public const TYPE_PLUS = 'plus';
    public const TYPE_MINUS = 'minus';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'source_type',
        'reference_id', // ID dari sumber (misalnya, transaction_id, review_id)
        'points',
        'points_type',
        'description',
        'created_at', // Jika Anda ingin mengisinya secara manual melalui create/update
    ];

    /**
     * Menunjukkan apakah model harus menggunakan timestamps (created_at dan updated_at).
     * Karena tabel Anda hanya memiliki 'created_at' dengan default dari database dan tidak ada 'updated_at',
     * ada beberapa cara untuk menanganinya:
     * 1. Set $timestamps = false; dan biarkan database menangani 'created_at' atau isi manual.
     * 2. Jika Anda ingin Eloquent HANYA mengelola 'created_at':
     */
    public $timestamps = true; // Aktifkan timestamps agar created_at bisa dikelola Eloquent
    const UPDATED_AT = null; // Beritahu Eloquent bahwa tidak ada kolom 'updated_at'

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'integer',
        'reference_id' => 'integer', // Tergantung tipe data ID referensi
        // 'created_at' akan otomatis di-cast ke Carbon instance jika $timestamps = true
    ];

    /**
     * Mendapatkan pengguna yang memiliki log poin ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendapatkan model sumber (parent) dari log poin ini secara polymorphic.
     * Misalnya, bisa Transaction, Review, atau model lain.
     *
     * Untuk menggunakan ini, pastikan kolom `source_type` diisi dengan
     * nama kelas model yang berelasi (misalnya, App\Models\Transaction::class atau aliasnya).
     * Namun, berdasarkan constraint CHECK Anda yang menggunakan string 'transaction', 'review', 'referral',
     * Anda mungkin perlu menyesuaikan bagaimana Anda menyimpan dan mengambil `source_type` jika
     * Anda tidak menyimpan nama kelas lengkap.
     *
     * Jika Anda tidak menggunakan nama kelas lengkap di `source_type`, Anda perlu
     * mengatur "morph map" di AppServiceProvider atau relasinya mungkin tidak bekerja
     * secara otomatis dengan `morphTo()`.
     *
     * Alternatifnya, jika relasinya tidak kompleks dan selalu merujuk ke ID,
     * Anda bisa membuat method terpisah untuk setiap source_type jika diperlukan,
     * atau membiarkannya sebagai ID generik.
     *
     * Jika `source_type` menyimpan string sederhana seperti 'transaction', 'review':
     * Maka Anda perlu mendefinisikan morph map di `AppServiceProvider` dalam method `boot()`:
     *
     * use Illuminate\Database\Eloquent\Relations\Relation;
     *
     * Relation::morphMap([
     * 'transaction' => 'App\Models\Transaction',
     * 'review' => 'App\Models\Review',
     * 'referral' => 'App\Models\Referral', // Ganti dengan model yang sesuai jika ada
     * ]);
     */
    public function loggable(): MorphTo
    {
        // Nama relasi, nama kolom type, nama kolom id
        return $this->morphTo(__FUNCTION__, 'source_type', 'reference_id');
    }
}
