<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Limbah untuk Toko: ') . $store->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-4">
                        <a href="{{ route('stores.show', $store->id) }}"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline mr-4">
                            &larr; Kembali ke Detail Toko
                        </a>
                        <a href="{{ route('stores.wastes.create', $store->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-500 active:bg-blue-700 dark:active:bg-blue-300 focus:outline-none focus:border-blue-700 dark:focus:border-blue-300 focus:ring ring-blue-300 dark:focus:ring-blue-600 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('+ Tambah Limbah Baru') }}
                        </a>
                    </div>

                    @if ($wastes->isEmpty())
                        <p>{{ __('Toko ini belum memiliki daftar limbah.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Nama Limbah</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Kategori</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Stok Utama</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Harga Default</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($wastes as $waste)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $waste->name }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $waste->category->name ?? '-' }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $waste->stock }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ ucfirst($waste->status) }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                Rp {{ number_format($waste->price, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('wastes.variants.index', $waste->id) }}"
                                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 mr-2">Kelola
                                                    Varian</a>
                                                {{-- Tambahkan link untuk edit/hapus waste jika perlu --}}
                                                {{-- <a href="{{ route('stores.wastes.edit', [$store->id, $waste->id]) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</a> --}}
                                                {{-- Form Hapus --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $wastes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
