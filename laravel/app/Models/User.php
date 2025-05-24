<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Pastikan use statement ini ada:
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'eco_points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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
}
