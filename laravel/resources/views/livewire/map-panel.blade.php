<div id="floating-panel" class="absolute top-0 left-0 h-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl shadow-2xl flex flex-col">
    <header class="p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <h1 class="text-xl font-extrabold text-green-600 dark:text-green-400">Eco Barter</h1>
        <nav class="mt-4 border-b border-gray-200 dark:border-gray-700">
            <div class="-mb-px flex space-x-6">
                <button wire:click="switchTab('search')" class="tab-button {{ $activeTab === 'search' ? 'active' : '' }}">Cari</button>
                <button wire:click="switchTab('detail')" class="tab-button {{ $activeTab === 'detail' ? 'active' : '' }}" {{ !$selectedWasteId ? 'disabled' : '' }}>Detail</button>
                <button wire:click="switchTab('profile')" class="tab-button {{ $activeTab === 'profile' ? 'active' : '' }}">Profil</button>
            </div>
        </nav>
    </header>

    <main class="flex-grow overflow-y-auto custom-scrollbar">
        <div class="{{ $activeTab === 'search' ? 'block' : 'hidden' }}">
            <div class="p-4 space-y-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                <input wire:model.live.debounce.500ms="q" type="text" placeholder="Cari nama sampah..." class="w-full form-input">
                <select wire:model.live="sortBy" class="w-full form-input">
                    <option value="">Urutkan: Relevansi</option>
                    <option value="price_asc">Harga Termurah</option>
                </select>
            </div>
            <div wire:loading class="p-8 text-center text-gray-500">Memuat...</div>
            <div wire:loading.remove>
                @forelse($wastes as $waste)
                    <div wire:click="showDetails({{ $waste->waste_id }})" class="result-card p-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 {{ $selectedWasteId == $waste->waste_id ? 'active' : '' }}">
                        <h3 class="font-bold text-gray-800 dark:text-white">{{ $waste->waste_name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $waste->store_name }}</p>
                        <span class="font-semibold text-green-600">{{-- Format harga --}}</span>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">Tidak ada hasil ditemukan.</div>
                @endforelse
                <div class="p-4">
                    {{ $wastes->links() }}
                </div>
            </div>
        </div>

        <div class="{{ $activeTab === 'detail' ? 'block' : 'hidden' }} p-6">
            @if($selectedWaste)
                <button wire:click="switchTab('search')" class="mb-4 text-sm font-semibold text-green-600"> &lt; Kembali</button>
                <h2 class="text-2xl font-bold">{{ $selectedWaste->name }}</h2>
                <p>Harga: {{ $selectedWaste->price }}</p>
                @endif
        </div>

        <div class="{{ $activeTab === 'profile' ? 'block' : 'hidden' }}">
            </div>
    </main>
</div>