{{-- File: resources/views/partials/technology.blade.php --}}
<section id="technology" class="py-16 md:py-24 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-3">
                Dibangun dengan Teknologi Terkini: Laravel 11
            </h2>
            <div class="inline-block w-24 h-1 bg-green-500 dark:bg-green-400 rounded"></div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12 bg-gray-50 dark:bg-gray-800 p-8 md:p-12 rounded-xl shadow-xl">
            <div class="md:w-1/3 text-center">
                <img src="{{ asset('images/laravel-logo.svg') }}" alt="Logo Laravel 11" class="mx-auto w-32 h-32 md:w-40 md:h-40 mb-4 filter hue-rotate-90 saturate-150"> {{-- Filter untuk nuansa hijau pada logo Laravel jika perlu --}}
                <h3 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">Laravel 11</h3>
            </div>
            <div class="md:w-2/3 text-gray-700 dark:text-gray-300 space-y-4 text-base md:text-lg">
                <p>
                    Platform [Nama Platform Anda] dikembangkan menggunakan <strong class="font-semibold text-green-600 dark:text-green-400">Laravel 11</strong>,
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
        </div>
    </div>
</section>