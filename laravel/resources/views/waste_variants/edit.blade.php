<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Varian Limbah untuk: ') }} <span class="font-bold">{{ $waste->name }}</span>
            <span class="text-base text-gray-600 dark:text-gray-400 block sm:inline sm:ml-2"> (Toko:
                {{ $waste->store->name }})</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST"
                        action="{{ route('wastes.variants.update', ['waste' => $waste->id, 'variant' => $variant->id]) }}">
                        @csrf
                        @method('PUT') {{-- atau PATCH --}}

                        <div>
                            <x-input-label for="volume_in_grams" :value="__('Volume (dalam gram)')" />
                            <x-text-input id="volume_in_grams" class="block mt-1 w-full" type="number"
                                name="volume_in_grams" :value="old('volume_in_grams', $variant->volume_in_grams)" required min="1" />
                            <x-input-error :messages="$errors->get('volume_in_grams')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="price" :value="__('Harga (Rp)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01"
                                name="price" :value="old('price', $variant->price)" required min="0" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="stock" :value="__('Stok Varian')" />
                            <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock"
                                :value="old('stock', $variant->stock)" required min="0" />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Stok utama limbah ini ({{ $waste->name }}) adalah:
                                <strong>{{ $waste->stock }}</strong>.
                                Total stok semua varian tidak boleh melebihi angka ini.
                            </p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('wastes.variants.index', $waste->id) }}"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Varian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
