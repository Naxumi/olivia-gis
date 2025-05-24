<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Toko: ') . $store->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div
                            class="mb-4 p-4 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-200 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h3 class="text-lg font-medium">{{ $store->name }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $store->address }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="font-semibold">Latitude:</span> {{ $store->latitude }}
                        </div>
                        <div>
                            <span class="font-semibold">Longitude:</span> {{ $store->longitude }}
                        </div>
                        <div>
                            <span class="font-semibold">Pemilik:</span> {{ $store->user->name }}
                        </div>
                        <div>
                            <span class="font-semibold">Terdaftar pada:</span>
                            {{ $store->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    {{-- Bagian untuk menampilkan limbah di toko ini --}}
                    <div class="mt-6">
                        <h4 class="text-md font-semibold mb-2">Daftar Limbah di Toko Ini:</h4>
                        <a href="{{ route('stores.wastes.create', $store->id) }}"
                            class="mb-4 inline-flex items-center px-3 py-1.5 bg-green-500 dark:bg-green-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-green-400 dark:hover:bg-green-500 active:bg-green-600 dark:active:bg-green-300 focus:outline-none focus:border-green-600 dark:focus:border-green-300 focus:ring ring-green-300 dark:focus:ring-green-600 disabled:opacity-25 transition ease-in-out duration-150">
                            + Tambah Limbah Baru
                        </a>
                        @if ($store->wastes->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada limbah yang ditambahkan di
                                toko ini.</p>
                        @else
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($store->wastes as $waste)
                                    <li>
                                        <a href="{{ route('wastes.variants.index', $waste->id) }}"
                                            class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $waste->name }} ({{ $waste->category->name }}) - Stok Utama:
                                            {{ $waste->stock }}
                                        </a>
                                        {{-- Link untuk edit/delete waste jika perlu --}}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>


                    <div class="mt-6 flex justify-between items-center">
                        <a href="{{ route('stores.index') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            &larr; {{ __('Kembali ke Daftar Toko') }}
                        </a>
                        <a href="{{ route('stores.edit', $store->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 dark:bg-yellow-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-yellow-400 dark:hover:bg-yellow-500 focus:outline-none focus:border-yellow-500 dark:focus:border-yellow-300 focus:ring ring-yellow-300 dark:focus:ring-yellow-600 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Edit Toko Ini') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
