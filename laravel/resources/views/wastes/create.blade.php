<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Limbah Baru untuk Toko: ') }} <span class="font-bold">{{ $store->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('stores.wastes.store', $store->id) }}">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nama Limbah')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name')" required autofocus autocomplete="off"
                                placeholder="Contoh: Botol Plastik PET Bekas, Kardus Bekas Layak Pakai" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="category_id" :value="__('Kategori Limbah')" />
                            <select id="category_id" name="category_id"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Deskripsi Tambahan (Opsional)')" />
                            <textarea id="description" name="description" rows="3"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                autocomplete="off">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="stock" :value="__('Stok Awal (Perkiraan Total Item Ini)')" />
                            <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock"
                                :value="old('stock', 0)" required min="0" />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Jumlah stok umum untuk jenis limbah
                                ini. Varian spesifik akan memiliki detail stok sendiri.</p>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="price" :value="__('Harga Dasar per Unit Umum (Rp)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01"
                                name="price" :value="old('price', 0.0)" required min="0" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Harga dasar umum. Varian spesifik
                                bisa memiliki harga berbeda.</p>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status Awal')" />
                            <select id="status" name="status"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>
                                <option value="available"
                                    {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Tersedia
                                    (Available)</option>
                                <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa
                                    (Expired)</option>
                                {{-- 'sold' mungkin tidak dipilih saat create --}}
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('stores.wastes.index', $store->id) }}"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Limbah') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
