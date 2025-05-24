<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Varian untuk Limbah: ') }} <span class="font-bold">{{ $waste->name }}</span>
            <span class="text-base text-gray-600 dark:text-gray-400 block sm:inline sm:ml-2"> (Toko:
                {{ $waste->store->name }})</span>
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

                    <div class="mb-4 flex justify-between items-center">
                        <a href="{{ route('stores.wastes.show', ['store' => $waste->store_id, 'waste' => $waste->id]) }}"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            &larr; Kembali ke Detail Limbah Induk
                        </a>
                        <a href="{{ route('wastes.variants.create', $waste->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-500 active:bg-blue-700 dark:active:bg-blue-300 focus:outline-none focus:border-blue-700 dark:focus:border-blue-300 focus:ring ring-blue-300 dark:focus:ring-blue-600 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('+ Tambah Varian Baru') }}
                        </a>
                    </div>

                    @if ($variants->isEmpty())
                        <p>{{ __('Belum ada varian yang ditambahkan untuk jenis limbah ini.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Volume (gram)
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Harga (Rp)
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Stok Varian
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Ditambahkan Pada
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Aksi</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($variants as $variant)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ number_format($variant->volume_in_grams, 0, ',', '.') }} gram
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                Rp {{ number_format($variant->price, 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $variant->stock }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $variant->created_at ? $variant->created_at->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('wastes.variants.edit', ['waste' => $waste->id, 'variant' => $variant->id]) }}"
                                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-200 mr-2">Edit</a>
                                                {{-- Anda perlu method destroy di WasteVariantController dan route-nya --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('wastes.variants.show', ['waste' => $waste->id, 'variant' => $variant->id]) }}"
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-200 mr-2">Lihat</a>
                                                <a href="{{ route('wastes.variants.edit', ['waste' => $waste->id, 'variant' => $variant->id]) }}"
                                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-200 mr-2">Edit</a>
                                                {{-- AKTIFKAN BAGIAN INI --}}
                                                <form
                                                    action="{{ route('wastes.variants.destroy', ['waste' => $waste->id, 'variant' => $variant->id]) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus varian ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">Hapus</button>
                                                </form>
                                            </td>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $variants->links() }} {{-- Tampilkan link paginasi --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
