<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Waste;          // 👈 Impor model Waste
use App\Models\WasteVariant;  // 👈 Impor model WasteVariant
use Illuminate\Support\Facades\Auth; // 👈 Impor Auth

class UpdateWasteVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\WasteVariant $variantBeingUpdated */
        $variantBeingUpdated = $this->route('variant'); // Mengambil objek WasteVariant dari rute

        // Otorisasi: User harus pemilik dari toko (store) yang memiliki waste dari varian ini
        // atau seorang admin.
        if ($variantBeingUpdated && $variantBeingUpdated->waste && $variantBeingUpdated->waste->store) {
            return Auth::id() === $variantBeingUpdated->waste->store->user_id || Auth::user()->roles()->where('name', 'admin')->exists();
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'volume_in_grams' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', // Stok baru untuk varian yang diupdate ini
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator) {
                /** @var \App\Models\WasteVariant $variantBeingUpdated */
                $variantBeingUpdated = $this->route('variant');
                /** @var \App\Models\Waste $parentWaste */
                $parentWaste = $variantBeingUpdated->waste; // Mengambil parent Waste dari relasi

                if (!$parentWaste || !$variantBeingUpdated || $validator->fails()) {
                    return;
                }

                $updatedStockForThisVariant = (int) $this->input('stock');

                // Hitung total stok dari varian lain (TIDAK TERMASUK varian yang sedang diupdate ini)
                $currentTotalOtherVariantsStock = $parentWaste->wasteVariants()
                                                    ->where('id', '!=', $variantBeingUpdated->id)
                                                    ->sum('stock');

                // Hitung total stok varian jika varian ini diupdate dengan stok baru
                $proposedTotalVariantStock = $currentTotalOtherVariantsStock + $updatedStockForThisVariant;

                if ($proposedTotalVariantStock > $parentWaste->stock) {
                    $availableForThisVariant = max(0, $parentWaste->stock - $currentTotalOtherVariantsStock);
                    $validator->errors()->add(
                        'stock',
                        "Total stok semua varian akan menjadi {$proposedTotalVariantStock}, " .
                        "yang melebihi stok utama limbah ({$parentWaste->stock}). " .
                        "Stok varian lain yang sudah ada: {$currentTotalOtherVariantsStock}. " .
                        "Anda dapat mengupdate stok varian ini menjadi maksimal {$availableForThisVariant}."
                    );
                }
            }
        ];
    }
}