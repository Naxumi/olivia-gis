<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Toko: ') }} <span class="text-indigo-600 dark:text-indigo-400">{{ $store->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div
                            class="mb-6 p-4 flex items-center bg-green-100 dark:bg-green-800 border border-green-300 dark:border-green-600 text-green-700 dark:text-green-100 rounded-lg shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-3 flex-shrink-0">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.06 0l4.073-5.576z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Detail Informasi Toko --}}
                    <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $store->name }}</h3>
                        <p class="mt-1 text-md text-gray-600 dark:text-gray-400">{{ $store->address }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mb-8">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Latitude</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $store->latitude }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Longitude</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $store->longitude }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemilik</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $store->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terdaftar pada</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $store->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                    </div>

                    {{-- Bagian untuk menampilkan limbah di toko ini --}}
                    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Daftar Limbah di Toko Ini:</h4>
                            <a href="{{ route('stores.wastes.create', $store->id) }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 active:bg-green-800 dark:active:bg-green-400 focus:outline-none focus:border-green-700 dark:focus:border-green-600 focus:ring ring-green-300 dark:focus:ring-green-700 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-2 -ml-1">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                Tambah Limbah
                            </a>
                        </div>

                        @if ($store->wastes->isEmpty())
                            <div class="text-center py-10 my-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /> {{-- Ikon contoh, bisa diganti --}}
                                </svg>
                                <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada limbah.</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan limbah baru untuk toko ini.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($store->wastes as $waste)
                                    <div class="bg-gray-50 dark:bg-gray-700/60 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-150 ease-in-out">
                                        <a href="{{ route('wastes.variants.index', $waste->id) }}"
                                            class="block text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $waste->name }}</span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ $waste->category->name }})</span>
                                                </div>
                                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                                    Stok Utama: <span class="font-bold">{{ $waste->stock }}</span>
                                                </div>
                                            </div>
                                        </a>
                                        {{-- Pertimbangkan untuk menambahkan link edit/delete waste di sini dengan ikon kecil --}}
                                        {{-- Contoh:
                                        <div class="mt-2 text-right space-x-2">
                                            <a href="#" class="text-xs text-yellow-600 hover:text-yellow-800">Edit</a>
                                            <form action="#" method="POST" class="inline"> @csrf @method('DELETE') <button type="submit" class="text-xs text-red-600 hover:red-800">Hapus</button></form>
                                        </div>
                                        --}}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>


                    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <a href="{{ route('stores.index') }}"
                            class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5">
                                <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.56l2.72 2.72a.75.75 0 11-1.06 1.06l-4.25-4.25a.75.75 0 010-1.06l4.25-4.25a.75.75 0 111.06 1.06L5.56 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Kembali ke Daftar Toko') }}
                        </a>
                        <a href="{{ route('stores.edit', $store->id) }}"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-yellow-500 dark:bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-yellow-600 dark:hover:bg-yellow-500 active:bg-yellow-700 dark:active:bg-yellow-400 focus:outline-none focus:border-yellow-700 dark:focus:border-yellow-500 focus:ring ring-yellow-300 dark:focus:ring-yellow-700 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Edit Toko Ini') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>