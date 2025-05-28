{{-- File: resources/views/landing.blade.php --}}
<x-guest-layout> {{-- Menggunakan layout guest baru --}}

    {{-- Konten Utama Landing Page --}}
    <div class="bg-white dark:bg-gray-800">
        @include('partials.hero')
        @include('partials.about')
        @include('partials.how_it_works')
        @include('partials.features')
        @include('partials.technology')
        @include('partials.olivia2025_message')
        @include('partials.cta_final')
    </div>

    @include('partials.footer')

</x-guest-layout>