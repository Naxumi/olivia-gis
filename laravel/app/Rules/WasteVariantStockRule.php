<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Waste; // Asumsi model Waste Anda
use App\Models\WasteVariant; // Asumsi model WasteVariant Anda
use Illuminate\Support\Facades\DB;

class WasteVariantStockRule implements ValidationRule
{
    protected $wasteId;
    protected $currentVariantId; // Untuk kasus update

    /**
     * Create a new rule instance.
     *
     * @param int $wasteId
     * @param int|null $currentVariantId ID dari waste variant yang sedang diupdate (jika ada)
     * @return void
     */
    public function __construct(int $wasteId, ?int $currentVariantId = null)
    {
        $this->wasteId = $wasteId;
        $this->currentVariantId = $currentVariantId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $waste = Waste::find($this->wasteId);

        if (!$waste) {
            // Seharusnya tidak terjadi jika waste_id sudah divalidasi exists:wastes,id
            // Tapi baik untuk defensive programming
            $fail('Limbah utama tidak ditemukan.');
            return;
        }

        $mainStock = $waste->stock;
        $newVariantStock = (int) $value; // Stok baru untuk varian ini

        // Hitung total stok varian yang sudah ada untuk waste_id ini,
        // kecuali varian yang sedang diupdate (jika ada)
        $otherVariantsStock = WasteVariant::where('waste_id', $this->wasteId)
            ->when($this->currentVariantId, function ($query) {
                return $query->where('id', '!=', $this->currentVariantId);
            })
            ->sum('stock');

        $totalProspectiveVariantStock = $otherVariantsStock + $newVariantStock;

        if ($totalProspectiveVariantStock > $mainStock) {
            $fail("Total stok varian (:total_variant_stock) tidak boleh melebihi stok utama (:main_stock). Sisa slot stok: :remaining_stock")
                ->translate([
                    'total_variant_stock' => $totalProspectiveVariantStock,
                    'main_stock' => $mainStock,
                    'remaining_stock' => max(0, $mainStock - $otherVariantsStock)
                ]);
        }
    }
}
