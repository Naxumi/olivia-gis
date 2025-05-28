{{-- File: resources/views/partials/about.blade.php --}}
<section id="about" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-3">
                Inovasi Hijau untuk Negeri & Semangat Olivia 2025
            </h2>
            <div class="inline-block w-24 h-1 bg-green-500 dark:bg-green-400 rounded"></div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12">
            <div class="md:w-1/2 text-gray-700 dark:text-gray-300 space-y-5 text-base md:text-lg leading-relaxed">
                <p>Pengelolaan sampah industri menjadi tantangan krusial di Indonesia. Volume sampah yang terus meningkat, kurangnya transparansi alur material, dan kesulitan mempertemukan penghasil sampah dengan industri pengolah yang tepat seringkali menyebabkan penumpukan sampah, pencemaran lingkungan, dan hilangnya potensi nilai ekonomi dari material daur ulang.</p>
                <p>
                    <strong class="font-semibold text-green-600 dark:text-green-400">[Nama Platform Anda]</strong> hadir sebagai solusi B2B GIS yang inovatif, dirancang untuk menjembatani kesenjangan ini. Kami menghubungkan industri penghasil sampah (seperti manufaktur, konstruksi, pertanian) dengan industri pengolah dan pendaur ulang sampah. Dengan memanfaatkan Sistem Informasi Geografis (GIS), platform kami mengoptimalkan logistik, meningkatkan visibilitas, dan memfasilitasi transaksi material sampah yang efisien.
                </p>
                <p>
                    Inisiatif ini sangat selaras dengan tema Olimpiade Vokasi Indonesia (OLIVIA) X 2025, <strong class="font-semibold text-green-600 dark:text-green-400">"Unlocking the Wonders of Tomorrow: Bridging Innovation and Sustainability"</strong>. Kami tidak hanya menciptakan inovasi teknologi, tetapi juga secara aktif mendorong praktik ekonomi sirkular dan pengelolaan sampah yang berkelanjutan, memberikan dampak positif bagi lingkungan dan ekonomi.
                </p>
            </div>
            <div class="md:w-1/2 mt-8 md:mt-0">
                <img src="{{ asset('images/waste-cycle-illustration.png') }}" alt="Ilustrasi Siklus Pengelolaan Sampah Berkelanjutan" class="rounded-lg shadow-xl mx-auto w-full max-w-md object-contain">
                 {{-- Ganti 'innovation-sustainability.svg' dengan gambar yang lebih relevan dengan tema sampah/daur ulang --}}
            </div>
        </div>
    </div>
</section>