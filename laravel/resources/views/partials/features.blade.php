{{-- File: resources/views/partials/features.blade.php --}}
<section id="features" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-3">
                Fitur Unggulan untuk Ekosistem Sampah Terintegrasi
            </h2>
            <div class="inline-block w-24 h-1 bg-green-500 dark:bg-green-400 rounded"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @php
                $features = [
                    ['icon' => 'fas fa-map-pin', 'title' => 'Pemetaan Sumber & Jenis Sampah', 'description' => 'Identifikasi dan visualisasikan lokasi penghasil sampah beserta jenis dan volume material yang dihasilkan secara akurat.'],
                    ['icon' => 'fas fa-truck-loading', 'title' => 'Optimasi Rute Pengangkutan', 'description' => 'Rencanakan rute penjemputan dan pengiriman sampah yang paling efisien untuk mengurangi biaya dan emisi karbon.'],
                    ['icon' => 'fas fa-balance-scale-right', 'title' => 'Marketplace Sampah Terverifikasi', 'description' => 'Platform transaksi aman antara penghasil dan pengolah sampah terverifikasi dengan sistem rating dan ulasan.'],
                    ['icon' => 'fas fa-chart-line', 'title' => 'Analitik & Pelaporan Daur Ulang', 'description' => 'Pantau metrik daur ulang, lacak alur material, dan hasilkan laporan dampak lingkungan secara transparan.'],
                    ['icon' => 'fas fa-comments', 'title' => 'Komunikasi & Negosiasi Terpadu', 'description' => 'Fasilitas komunikasi langsung dan negosiasi harga antara pihak-pihak terkait dalam platform.'],
                    ['icon' => 'fas fa-shield-alt', 'title' => 'Kepatuhan & Keamanan Regulasi', 'description' => 'Mendukung pelaporan kepatuhan regulasi pengelolaan sampah dan memastikan keamanan data transaksi.'],
                ];
            @endphp

            @foreach ($features as $feature)
            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 flex flex-col items-center text-center">
                <div class="flex items-center justify-center w-16 h-16 mb-5 rounded-full bg-green-100 dark:bg-green-600 text-green-500 dark:text-green-200 text-3xl">
                    <i class="{{ $feature['icon'] }}"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">{{ $feature['title'] }}</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{{ $feature['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>