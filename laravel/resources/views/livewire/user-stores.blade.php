<div>
    @if(session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h4 class="font-bold text-lg text-gray-800 dark:text-gray-200">Toko Saya</h4>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Tambah Toko</button>
    </div>

    <div class="space-y-4">
        @forelse($stores as $store)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $store->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $store->address }}</p>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="edit({{ $store->id }})" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button wire:click="confirmStoreDeletion({{ $store->id }})" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">Anda belum memiliki toko.</p>
        @endforelse
    </div>

    @if($showStoreModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6" @click.away="$wire.showStoreModal = false">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $editableStore->exists ? 'Edit Toko' : 'Tambah Toko Baru' }}</h3>
                <form wire:submit.prevent="saveStore" class="mt-4 space-y-4">
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Toko</label>
                        <input type="text" wire:model.defer="editableStore.name" id="store_name" class="mt-1 block w-full form-input">
                        @error('editableStore.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="store_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Toko</label>
                        <textarea wire:model.defer="editableStore.address" id="store_address" class="mt-1 block w-full form-input"></textarea>
                        @error('editableStore.address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" wire:click="$set('showStoreModal', false)" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md text-sm font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($confirmingStoreDeletion)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hapus Toko?</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Apakah Anda yakin ingin menghapus toko ini? Semua limbah yang terkait juga akan dihapus.</p>
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" wire:click="$set('confirmingStoreDeletion', false)" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md text-sm font-medium">Batal</button>
                    <button type="button" wire:click="deleteStore" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>