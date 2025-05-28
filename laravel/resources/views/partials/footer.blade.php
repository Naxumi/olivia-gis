{{-- File: resources/views/partials/footer_tailwind.blade.php --}}
<footer class="bg-gray-800 dark:bg-black text-gray-400 dark:text-gray-500 py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center text-center md:text-left">
            <div class="mb-4 md:mb-0">
                <p class="text-sm">&copy; {{ date('Y') }} GreenFlag. Solusi Inovatif Pengelolaan Sampah.</p>
                <p class="text-xs mt-1">Sebuah Proyek untuk Olimpiade Vokasi Indonesia X - 2025.</p>
            </div>
            <div class="flex items-center space-x-4">
                @if(asset('images/olivia2025-logo-small.png'))
                    <img src="{{ asset('images/olivia2025-logo-small.png') }}" alt="Logo Olivia 2025" class="h-8 md:h-9 opacity-75">
                @endif
            </div>
        </div>
    </div>
</footer>