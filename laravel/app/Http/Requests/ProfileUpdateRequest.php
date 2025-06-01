<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // Kolom baru untuk diedit di profil
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address_detail' => ['nullable', 'string', 'max:1000'],
            'village' => ['nullable', 'string', 'max:255'],
            'subdistrict' => ['nullable', 'string', 'max:255'],
            'city_regency' => ['nullable', 'string', 'max:255'], // Mungkin 'required' tergantung kebutuhan
            'province' => ['nullable', 'string', 'max:255'],     // Mungkin 'required'
            'postal_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['required', 'numeric', 'between:-90,90'], // Nullable jika pengguna boleh tidak mengisi
            'longitude' => ['required', 'numeric', 'between:-180,180'], // Nullable
            'address_notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Atribut kustom untuk pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'phone_number' => 'Nomor Telepon',
            'address_detail' => 'Detail Alamat',
            'village' => 'Desa/Kelurahan',
            'subdistrict' => 'Kecamatan',
            'city_regency' => 'Kota/Kabupaten',
            'province' => 'Provinsi',
            'postal_code' => 'Kode Pos',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'address_notes' => 'Catatan Alamat',
        ];
    }
}
