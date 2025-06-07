<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Import File facade
use Symfony\Component\HttpFoundation\BinaryFileResponse; // Untuk type-hint

class DocumentController extends Controller
{
    /**
     * Menampilkan file dokumentasi teknis.
     */
    public function showTechnicalDocumentation(): BinaryFileResponse
    {
        // Path ke file di dalam folder public
        $path = public_path('documents/Dokumentasi_Teknis.pdf');

        // Periksa apakah file benar-benar ada sebelum menampilkannya
        if (!File::exists($path)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // Return file sebagai response. Browser akan menampilkannya inline jika mendukung.
        return response()->file($path);
    }
}
