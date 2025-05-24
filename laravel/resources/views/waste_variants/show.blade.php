<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Varian Limbah: ') }} <span
                class="font-mono">{{ number_format($variant->volume_in_grams, 0, ',', '.') }} gram</span>
            <span class="text-base text-gray-600 dark:text-gray-400 block sm:inline sm:ml-2">
                (Untuk Limbah: <span class="font-bold">{{ $waste->name }}</span>, Toko: {{ $waste->store->name }})
            </span>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informasi Varian</h3>
                            <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">ID Varian</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $variant->id }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Volume</dt>
                                    <dd class="text-gray-900 dark:text-white">
                                        {{ number_format($variant->volume_in_grams, 0, ',', '.') }} gram</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Harga</dt>
                                    <dd class="text-gray-900 dark:text-white">Rp
                                        {{ number_format($variant->price, 0, ',', '.') }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Stok Saat Ini</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $variant->stock }} unit</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Ditambahkan Pada</dt>
                                    <dd class="text-gray-900 dark:text-white">
                                        {{ $variant->created_at ? $variant->created_at->format('d M Y, H:i') : '-' }}
                                    </dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                                    <dd class="text-gray-900 dark:text-white">
                                        {{ $variant->updated_at ? $variant->updated_at->format('d M Y, H:i') : '-' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informasi Limbah Induk</h3>
                            <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Nama Limbah</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $waste->name }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Kategori</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $waste->category->name ?? '-' }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Stok Utama Limbah</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $waste->stock }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Deskripsi Limbah</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $waste->description ?: '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div
                        class="mt-6 flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('wastes.variants.index', $waste->id) }}"
                            class="w-full sm:w-auto text-center underline text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            &larr; {{ __('Kembali ke Daftar Varian') }}
                        </a>
                        <div class="w-full sm:w-auto flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('wastes.variants.edit', ['waste' => $waste->id, 'variant' => $variant->id]) }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-yellow-500 dark:bg-yellow-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-yellow-400 dark:hover:bg-yellow-500 focus:outline-none focus:border-yellow-500 dark:focus:border-yellow-300 focus:ring ring-yellow-300 dark:focus:ring-yellow-600 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Edit Varian Ini') }}
                            </a>
                            {{-- Tombol Hapus --}}
                            <form
                                action="{{ route('wastes.variants.destroy', ['waste' => $waste->id, 'variant' => $variant->id]) }}"
                                method="POST" class="w-full sm:w-auto"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus varian limbah ini? Semua data terkait akan hilang.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 dark:hover:bg-red-400 active:bg-red-700 dark:active:bg-red-300 focus:outline-none focus:border-red-700 dark:focus:border-red-300 focus:ring ring-red-300 dark:focus:ring-red-600 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Hapus Varian Ini') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
