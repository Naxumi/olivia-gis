<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Pastikan use statement ini ada:
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point dari Magellan
use Laravel\Sanctum\HasApiTokens; // <-- TAMBAHKAN IMPORT INI
use Spatie\Permission\Traits\HasRoles; // <-- PASTIKAN INI JUGA ADA JIKA ANDA PAKAI SPATIE


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens; // <-- TAMBAHKAN HASAPITOKENS DI SINI

    protected $fillable = [
        'name',
        'email',
        'password',
        'eco_points',
        'phone_number',
        'address_detail',
        'village',
        'subdistrict',
        'city_regency',
        'province',
        'postal_code',
        'location', // Nama kolom PostGIS Anda
        'address_notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'eco_points' => 'integer',
            'location' => Point::class, // Casting ke objek Point dari Magellan
        ];
    }

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Role>
     */
    public function roles(): BelongsToMany // Pastikan return type hint ini juga ada
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Get the stores for the user (seller).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Store>
     */
    public function stores(): HasMany // Pastikan return type hint ini juga ada
    {
        return $this->hasMany(Store::class);
    }
    // Tambahkan relasi lain jika perlu (transactions as buyer/seller, etc.)
    public function ecoPointLogs(): HasMany
    {
        return $this->hasMany(EcoPointLog::class);
    }
    // Relasi untuk transaksi (opsional, bisa juga diakses dari Transaction model)
    public function transactionsAsBuyer(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    public function transactionsAsSeller(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }
    public function distributorProfile()
    {
        return $this->hasOne(DistributorProfile::class); // Jika ada model DistributorProfile
    }
}
