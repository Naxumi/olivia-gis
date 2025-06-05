{{-- File: resources/views/partials/hero.blade.php --}}
<section id="hero"
    class="bg-gradient-to-br from-green-600 to-emerald-700 dark:from-green-700 dark:to-emerald-800 text-white pt-20 pb-16 md:pt-28 md:pb-24">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold mb-6 leading-tight tracking-tight">
                Revolusi Pengelolaan Sampah: <span class="block sm:inline"><span id="typing-text-placeholder"></span><span
                        class="typing-cursor">|</span></span>
            </h1>
            <p class="text-lg md:text-xl mb-10 text-emerald-100 dark:text-emerald-200 max-w-2xl mx-auto">
                Menghubungkan industri penghasil sampah dengan pengolah sampah melalui solusi GIS cerdas dan
                berkelanjutan.
                Kontribusi nyata untuk ekonomi sirkular dan tema Olivia 2025.
            </p>
            <div class="space-y-4 sm:space-y-0 sm:flex sm:justify-center sm:space-x-4">
                <a href="#features"
                    class="inline-block bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300 transform hover:scale-105 text-lg w-full sm:w-auto">
                    Pelajari Solusi Kami
                </a>
                <a href="#cta-final"
                    class="inline-block bg-transparent hover:bg-yellow-400 text-yellow-400 hover:text-gray-900 font-semibold py-3 px-8 rounded-lg border-2 border-yellow-400 transition duration-300 text-lg w-full sm:w-auto">
                    Hubungi Kami
                </a>
                <a href="{{ route('map.interactive') }}" {{-- Menggunakan nama route --}}
                    class="inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300 transform hover:scale-105 text-lg w-full sm:w-auto mt-4 sm:mt-0">
                    <i class="fas fa-map-marked-alt mr-2"></i> Lihat Peta Interaktif
                </a>
            </div>
        </div>
        <div class="mt-12 md:mt-16">
            <img src="{{ asset('images/hero-waste-management.png') }}" alt="Platform B2B GIS untuk Pengelolaan Sampah"
                class="mx-auto rounded-lg shadow-2xl max-w-xs sm:max-w-sm md:max-w-md lg:max-w-xl xl:max-w-2xl transform hover:scale-105 transition-transform duration-500">
            {{-- Ganti 'hero-mockup.png' dengan gambar yang lebih relevan dengan tema sampah/daur ulang --}}
            <p class="text-xs text-emerald-200 dark:text-emerald-300 mt-2">Solusi digital untuk rantai pasok sampah yang
                efisien.</p>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const words = ["Platform B2B GIS Inovatif", "Solusi Sampah Cerdas", "Ekonomi Sirkular Digital"];
            let i = 0;
            let j = 0;
            let currentWord = "";
            let isDeleting = false;
            const placeholder = document.getElementById('typing-text-placeholder');
            const cursor = document.querySelector('.typing-cursor');

            function type() {
                currentWord = words[i];
                if (isDeleting) {
                    placeholder.textContent = currentWord.substring(0, j - 1);
                    j--;
                    if (j == 0) {
                        isDeleting = false;
                        i++;
                        if (i == words.length) {
                            i = 0;
                        }
                    }
                } else {
                    placeholder.textContent = currentWord.substring(0, j + 1);
                    j++;
                    if (j == currentWord.length) {
                        isDeleting = true;
                        // Tambahkan jeda sebelum mulai menghapus
                        setTimeout(type, 2000); // Jeda 2 detik
                        return;
                    }
                }
                // Atur kecepatan mengetik dan menghapus
                const typingSpeed = isDeleting ? 100 : 200;
                setTimeout(type, typingSpeed);
            }

            if (placeholder) {
                type();
                // Efek kedip untuk kursor
                setInterval(() => {
                    cursor.style.opacity = cursor.style.opacity == '0' ? '1' : '0';
                }, 500);
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .typing-cursor {
            display: inline-block;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {

            from,
            to {
                opacity: 1
            }

            50% {
                opacity: 0
            }
        }
    </style>
@endpush
