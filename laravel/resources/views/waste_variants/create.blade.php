<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Varian Baru untuk Limbah: ') }} <span class="font-bold">{{ $waste->name }}</span>
            <span class="text-base text-gray-600 dark:text-gray-400 block sm:inline sm:ml-2"> (Toko:
                {{ $waste->store->name }})</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div
                        class="mb-4 p-4 bg-blue-100 dark:bg-blue-900 border border-blue-300 dark:border-blue-700 rounded-md">
                        <h4 class="font-semibold text-md text-blue-700 dark:text-blue-300">Informasi Limbah Induk:</h4>
                        <p class="text-sm text-blue-600 dark:text-blue-400">Nama Limbah: <span
                                class="font-medium">{{ $waste->name }}</span></p>
                        <p class="text-sm text-blue-600 dark:text-blue-400">Kategori: <span
                                class="font-medium">{{ $waste->category->name ?? '-' }}</span></p>
                        <p class="text-sm text-blue-600 dark:text-blue-400">Stok Utama Limbah Saat Ini: <span
                                class="font-medium">{{ $waste->stock }}</span> unit umum</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total stok semua varian yang Anda
                            tambahkan untuk limbah ini tidak boleh melebihi stok utama.</p>
                    </div>

                    <form method="POST" action="{{ route('wastes.variants.store', $waste->id) }}">
                        @csrf

                        <div>
                            <x-input-label for="volume_in_grams" :value="__('Volume Varian (dalam gram)')" />
                            <x-text-input id="volume_in_grams" class="block mt-1 w-full" type="number"
                                name="volume_in_grams" :value="old('volume_in_grams')" required autofocus min="1"
                                placeholder="Contoh: 1000 untuk 1kg, 500 untuk 500g" />
                            <x-input-error :messages="$errors->get('volume_in_grams')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="price" :value="__('Harga Varian (Rp)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01"
                                name="price" :value="old('price')" required min="0"
                                placeholder="Contoh: 2500.00" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="stock" :value="__('Stok untuk Varian Ini')" />
                            <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock"
                                :value="old('stock')" required min="0" />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            @if ($errors->has('stock'))
                                {{-- Menampilkan error kustom dari FormRequest after hook --}}
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $errors->first('stock') }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('wastes.variants.index', $waste->id) }}"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Varian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
