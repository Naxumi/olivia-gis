{{-- File: resources/views/partials/technology.blade.php --}}
<section id="technology" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Judul Utama Section --}}
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-3">
                Tumpuan Teknologi Kami
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Platform kami dibangun di atas fondasi teknologi yang andal, modern, dan dapat diskalakan untuk merevolusi pengelolaan limbah.
            </p>
            <div class="inline-block w-24 h-1 bg-green-500 dark:bg-green-400 rounded mt-4"></div>
        </div>

        {{-- Teknologi Backend --}}
        <div class="mb-16">
            <h3 class="text-2xl md:text-3xl font-semibold text-center text-green-700 dark:text-green-400 mb-10">
                Backend & Database
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="{{ asset('images/laravel-logo.svg') }}" alt="Logo Laravel 12" class="h-16 mx-auto mb-4">
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Laravel 12</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Framework PHP modern sebagai inti aplikasi, menyediakan struktur yang elegan, aman, dan berperforma tinggi.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                     <img src="{{ asset('images/php-logo.svg') }}" alt="Logo PHP 8.2" class="h-16 mx-auto mb-4">
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">PHP 8.2</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Bahasa pemrograman server-side yang matang dengan peningkatan performa signifikan dan fitur-fitur modern.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/29/Postgresql_elephant.svg" alt="Logo PostgreSQL" class="h-16 mx-auto mb-4">
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">PostgreSQL + PostGIS</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Database relasional yang kuat dengan ekstensi PostGIS untuk menyimpan dan melakukan kueri data geospasial secara efisien.
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <div class="text-green-500 mx-auto mb-4 flex items-center justify-center h-16 w-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Laravel Sanctum</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Sistem autentikasi ringan untuk mengamankan API yang digunakan oleh frontend, memastikan data tetap aman.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <div class="text-green-500 mx-auto mb-4 flex items-center justify-center h-16 w-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Laravel Magellan</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Toolbox PostGIS modern untuk Laravel, mempermudah operasi geospasial seperti pencarian berbasis lokasi.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <div class="text-green-500 mx-auto mb-4 flex items-center justify-center h-16 w-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-1.78-4.125" /></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Spatie Permission</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Paket untuk manajemen peran (roles) dan izin (permissions), memisahkan hak akses antar pengguna secara rapi.
                    </p>
                </div>
            </div>
            <div class="md:w-2/3 text-gray-700 dark:text-gray-300 space-y-4 text-base md:text-lg">
                <p>
                    Platform Eco Barter dikembangkan menggunakan <strong class="font-semibold text-green-600 dark:text-green-400">Laravel 11</strong>,
                    framework PHP modern yang terkenal akan performa, keamanan, dan skalabilitasnya.
                    Kami memilih Laravel 11 karena struktur aplikasinya yang lebih ramping, peningkatan kinerja yang signifikan,
                    serta fitur-fitur terbaru yang memungkinkan pengembangan yang lebih cepat dan efisien untuk platform pengelolaan sampah yang kompleks.
                </p>
                <p>
                    Dengan Laravel 11, kami dapat membangun aplikasi web yang tidak hanya andal dan aman,
                    tetapi juga siap untuk pengembangan dan penambahan fitur di masa depan (*future-proof*),
                    memastikan platform kami tetap relevan dan kompetitif dalam mendukung ekonomi sirkular.
                </p>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 pt-2">
                    <li class="flex items-center"><i class="fas fa-rocket text-green-500 dark:text-green-400 mr-2"></i>Performa Tinggi & Efisien</li>
                    <li class="flex items-center"><i class="fas fa-shield-alt text-green-500 dark:text-green-400 mr-2"></i>Keamanan Bawaan yang Tangguh</li>
                    <li class="flex items-center"><i class="fas fa-expand-arrows-alt text-green-500 dark:text-green-400 mr-2"></i>Skalabilitas untuk Pertumbuhan</li>
                    <li class="flex items-center"><i class="fas fa-code text-green-500 dark:text-green-400 mr-2"></i>Ekosistem Pengembangan Produktif</li>
                </ul>
        </div>

        {{-- Teknologi Frontend --}}
        <div>
            <h3 class="text-2xl md:text-3xl font-semibold text-center text-green-700 dark:text-green-400 mb-10">
                Frontend & Peta Interaktif
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://leafletjs.com/docs/images/logo.png" alt="Logo Leaflet.js" class="h-16 mx-auto mb-4">
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Leaflet.js</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Pustaka JavaScript open-source yang ringan untuk membangun peta interaktif yang mobile-friendly dan kaya fitur.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Openstreetmap_logo.svg/600px-Openstreetmap_logo.svg.png" alt="Logo OpenStreetMap" class="h-16 mx-auto mb-4">
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">OpenStreetMap</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Menyediakan data peta dasar yang gratis dan kolaboratif, menjadi fondasi visualisasi data spasial di platform kami.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d5/Tailwind_CSS_Logo.svg" alt="Logo Tailwind CSS" class="h-16 mx-auto mb-4">
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Tailwind CSS</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Framework CSS utility-first untuk membangun antarmuka pengguna yang modern dan responsif dengan cepat dan konsisten.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>