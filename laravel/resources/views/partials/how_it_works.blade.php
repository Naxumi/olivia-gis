{{-- File: resources/views/partials/how_it_works.blade.php --}}
<section id="how-it-works" class="py-16 md:py-24 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-3">
                Menghubungkan Penghasil & Pengolah Sampah dengan Presisi GIS
            </h2>
            <div class="inline-block w-24 h-1 bg-green-500 dark:bg-green-400 rounded"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            {{-- Kolom Untuk Penghasil Sampah --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-700 text-green-500 dark:text-green-300 mr-4">
                        <i class="fas fa-industry text-2xl"></i> {{-- Ikon untuk industri/penghasil --}}
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Untuk Penghasil Sampah</h3>
                </div>
                <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Registrasi jenis & volume sampah mudah.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Pemetaan lokasi pengumpulan sampah.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Notifikasi permintaan dari pengolah terdekat.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Jadwalkan penjemputan sampah secara efisien.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Akses ke jaringan pengolah terverifikasi.</li>
                </ul>
            </div>

            {{-- Kolom Untuk Pengolah Sampah --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-700 text-green-500 dark:text-green-300 mr-4">
                        <i class="fas fa-recycle text-2xl"></i> {{-- Ikon untuk daur ulang/pengolah --}}
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Untuk Pengolah Sampah</h3>
                </div>
                <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Pencarian sumber sampah spesifik berbasis lokasi.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Filter berdasarkan jenis, volume, dan kualitas sampah.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Optimasi rute pengambilan material sampah.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Manajemen pasokan bahan baku daur ulang.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Transaksi aman dan transparan dengan penghasil.</li>
                </ul>
            </div>

            {{-- Kolom Kekuatan GIS --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 md:col-span-2 lg:col-span-1">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-700 text-green-500 dark:text-green-300 mr-4">
                        <i class="fas fa-map-marked-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Kekuatan Inti GIS Platform</h3>
                </div>
                <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Visualisasi sebaran sumber & jenis sampah.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Analisis spasial untuk penentuan lokasi fasilitas pengolahan.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Optimasi logistik pengangkutan sampah yang efisien.</li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 mt-1 mr-2"></i>Dasbor pemantauan alur sampah secara *real-time*.</li>
                </ul>
            </div>
        </div>
         <div class="text-center mt-12">
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm">Platform kami mengubah sampah menjadi sumber daya berharga melalui koneksi B2B yang didukung GIS.</p>
        </div>
    </div>
</section>