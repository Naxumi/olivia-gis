<x-map-layout title="Peta & Info Pengelola Sampah (Floating Panel)">

    <style>
        /* CSS ini sudah benar, tidak perlu diubah */
        #mapContainer,
        #mapContainer .leaflet-tile-pane {
            filter: none !important;
            -webkit-filter: none !important;
        }
    </style>

    {{-- Container Utama --}}
    <div class="w-screen h-screen relative">

        {{-- Map Container (dengan perbaikan tanda kutip) --}}
        <div id="mapContainer" class="w-full h-full bg-gray-300 dark:bg-gray-500">
            {{-- Peta akan diinisialisasi di sini oleh Leaflet --}}
        </div>

        {{-- Floating Side Panel --}}
        <div id="sidePanel"
            class="absolute top-3 left-3 bottom-3 z-[1000]
                   w-full max-w-md sm:w-80 md:w-96
                   bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm
                   border border-gray-200 dark:border-gray-700 rounded-lg shadow-2xl
                   flex flex-col
                   transition-transform duration-300 ease-in-out
                   -translate-x-[110%] sm:translate-x-0">

            {{-- Header Panel --}}
            <div
                class="flex items-center justify-between p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">Informasi Detail</h2>
                <button id="closePanelButtonMobile"
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white sm:hidden">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Tab Buttons --}}
            <div class="flex border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <button data-tab-target="detailBarang"
                    class="tab-button active flex-1 py-3 px-2 text-xs sm:text-sm text-center">
                    <i class="fas fa-recycle mr-1"></i> Info Daur Ulang
                </button>
                <button data-tab-target="profilToko" class="tab-button flex-1 py-3 px-2 text-xs sm:text-sm text-center">
                    <i class="fas fa-industry mr-1"></i> Profil Pengolah
                </button>
            </div>

            {{-- Konten Tab (Scrollable) --}}
            <div class="flex-grow overflow-y-auto custom-scrollbar p-3 sm:p-4 space-y-4">
                {{-- Placeholder --}}
                <div id="initialContent" class="text-center text-gray-500 pt-10">
                    <i class="fas fa-map-marker-alt text-4xl mb-3"></i>
                    <p>Klik marker di peta untuk melihat detail.</p>
                </div>
                {{-- Konten Dinamis --}}
                <div id="detailBarang" class="tab-content hidden space-y-3"></div>
                <div id="profilToko" class="tab-content hidden space-y-3"></div>
            </div>
        </div>

        {{-- Tombol Open Panel (Mobile) --}}
        <button id="openPanelButton"
            class="absolute top-3 left-3 z-[1001]
                   sm:hidden p-2.5 bg-white dark:bg-gray-700 rounded-full shadow-lg text-green-600 dark:text-green-400">
            <i class="fas fa-bars text-lg"></i>
        </button>

        {{-- Search Bar --}}
        <div id="searchBarContainer"
            class="absolute top-3 z-[1000]
                   right-3 left-16 sm:left-[21.5rem] md:left-[25.5rem]
                   transition-all duration-300 ease-in-out">
            <div class="relative">
                <input type="text" placeholder="Cari lokasi atau pengelola..."
                    class="w-full p-2.5 pl-9 text-sm bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg focus:ring-green-500 focus:border-green-500 outline-none">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>

        {{-- Tombol Zoom Control --}}
        <div class="absolute bottom-3 right-3 z-[1000] flex flex-col space-y-2">
            <button id="zoomInButton"
                class="bg-white dark:bg-gray-800 p-2.5 w-10 h-10 rounded-lg shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-plus"></i>
            </button>
            <button id="zoomOutButton"
                class="bg-white dark:bg-gray-800 p-2.5 w-10 h-10 rounded-lg shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>


    {{-- 1. TOMBOL UNTUK MEMBUKA MODAL --}}
    <div class="absolute top-3 right-20 z-[1001]">
        @auth
            <button onclick="openManageStoresModal()"
                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-store mr-2"></i>
                Manajemen Toko Saya
            </button>
        @endauth
    </div>


    {{-- 2. CONTAINER MODAL (Awalnya tersembunyi) --}}
    <div id="modal-container"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-[2000] hidden items-center justify-center"
        onclick="closeModal(event)">
        <div id="modal-content"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            {{-- Konten modal akan dimasukkan di sini oleh JavaScript --}}
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- PERBAIKAN: Hanya gunakan SATU @push('scripts') --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // --- Referensi Elemen ---
                const mapContainer = document.getElementById('mapContainer');
                const sidePanel = document.getElementById('sidePanel');
                const openPanelButton = document.getElementById('openPanelButton');
                const closePanelButtonMobile = document.getElementById('closePanelButtonMobile');
                const searchBarContainer = document.getElementById('searchBarContainer');
                let map = null;

                // --- Data Contoh ---
                const locations = {
                    'A001': {
                        lat: -6.175110,
                        lng: 106.865036,
                        profil: {
                            nama: 'CV. Lestari Daur Ulang',
                            alamat: 'Jl. Industri Hijau No. 8, Jakarta'
                        },
                        produk: [{
                            id: 'POC001',
                            nama: 'Pupuk Organik Cair Super',
                            deskripsi: 'Dibuat dari limbah organik pilihan, menyuburkan tanaman secara alami.',
                            harga: 'Rp 25.000 / botol',
                            icon: 'fa-leaf text-green-500'
                        }]
                    },
                    'B002': {
                        lat: -7.946355,
                        lng: 112.641556,
                        profil: {
                            nama: 'Bank Sampah Sawojajar',
                            alamat: 'Jl. Danau Toba G1E18, Malang'
                        },
                        produk: [{
                            id: 'KMP001',
                            nama: 'Kerajinan Tangan Plastik',
                            deskripsi: 'Aneka kerajinan unik dari daur ulang botol plastik.',
                            harga: 'Mulai dari Rp 10.000',
                            icon: 'fa-gem text-blue-500'
                        }, {
                            id: 'KMP002',
                            nama: 'Tas Belanja Ecobag',
                            deskripsi: 'Tas belanja kuat dan ramah lingkungan dari bahan daur ulang.',
                            harga: 'Rp 15.000',
                            icon: 'fa-shopping-bag text-yellow-500'
                        }]
                    }
                };

                // --- Inisialisasi Peta ---
                function initializeMap() {
                    if (map) {
                        map.remove();
                        map = null;
                    }
                    map = L.map(mapContainer, {
                        zoomControl: false,
                        attributionControl: false
                    }).setView([-7.9839, 112.6213], 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
                    L.control.attribution({
                        prefix: '<a href="https://leafletjs.com">Leaflet</a>',
                        position: 'bottomright'
                    }).addTo(map);

                    for (const id in locations) {
                        const loc = locations[id];
                        const marker = L.marker([loc.lat, loc.lng]).addTo(map);
                        marker.on('click', () => {
                            updatePanelContent(id);
                            if (window.innerWidth < 640) openPanel();
                            map.flyTo([loc.lat, loc.lng], 15);
                        });
                    }
                    document.getElementById('zoomInButton').addEventListener('click', () => map.zoomIn());
                    document.getElementById('zoomOutButton').addEventListener('click', () => map.zoomOut());
                    setTimeout(() => {
                        map.invalidateSize(true);
                    }, 1);
                }

                // --- Fungsi Panel ---
                function forceMapResize() {
                    if (!map) return;
                    setTimeout(() => {
                        map.invalidateSize({
                            animate: true,
                            duration: 0.5
                        });
                    }, 350);
                }

                function openPanel() {
                    const isAlreadyOpen = !sidePanel.classList.contains('-translate-x-[110%]');
                    if (isAlreadyOpen && window.innerWidth >= 640) return;
                    sidePanel.classList.remove('-translate-x-[110%]');
                    searchBarContainer.classList.remove('sm:left-16');
                    searchBarContainer.classList.add('sm:left-[21.5rem]', 'md:left-[25.5rem]');
                    if (window.innerWidth < 640) {
                        openPanelButton.classList.add('opacity-0', 'pointer-events-none');
                    }
                    forceMapResize();
                }

                function closePanel() {
                    sidePanel.classList.add('-translate-x-[110%]');
                    searchBarContainer.classList.add('sm:left-16');
                    searchBarContainer.classList.remove('sm:left-[21.5rem]', 'md:left-[25.5rem]');
                    if (window.innerWidth < 640) {
                        openPanelButton.classList.remove('opacity-0', 'pointer-events-none');
                    }
                    forceMapResize();
                }

                // --- Update Konten Panel ---
                function updatePanelContent(locationId) {
                    const data = locations[locationId];
                    if (!data) return;
                    document.getElementById('initialContent').classList.add('hidden');
                    const detailContainer = document.getElementById('detailBarang');
                    detailContainer.innerHTML = '';
                    data.produk.forEach(item => {
                        detailContainer.innerHTML +=
                            `<div class="bg-gray-50 dark:bg-gray-700/70 p-3 rounded-lg shadow-md"><div class="flex justify-center mb-2"><i class="fas ${item.icon} text-4xl"></i></div><h3 class="text-md font-semibold text-green-600 dark:text-green-400 text-center">${item.nama}</h3><p class="text-xs text-gray-400 text-center mb-2">ID: ${item.id}</p><p class="text-sm text-gray-700 dark:text-gray-300">${item.deskripsi}</p><p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-2">Harga: ${item.harga}</p></div>`;
                    });
                    const profilContainer = document.getElementById('profilToko');
                    profilContainer.innerHTML =
                        `<div class="bg-gray-50 dark:bg-gray-700/70 p-3 rounded-lg shadow-md"><div class="flex justify-center mb-2"><i class="fas fa-industry text-4xl text-gray-500"></i></div><h3 class="text-md font-semibold text-green-600 dark:text-green-400 text-center">${data.profil.nama}</h3><p class="text-sm text-gray-700 dark:text-gray-300 mt-2"><i class="fas fa-map-marker-alt w-4 mr-1"></i>${data.profil.alamat}</p></div>`;
                    document.querySelectorAll('.tab-content').forEach(c => {
                        if (c.id !== 'initialContent') c.classList.remove('hidden')
                    });
                    document.querySelector('.tab-button[data-tab-target="detailBarang"]').click();
                }

                // --- Event Listeners ---
                openPanelButton.addEventListener('click', openPanel);
                closePanelButtonMobile.addEventListener('click', closePanel);
                const tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const targetId = button.getAttribute('data-tab-target');
                        tabButtons.forEach(btn => btn.classList.remove('active'));
                        document.querySelectorAll('.tab-content').forEach(content => content.classList
                            .add('hidden'));
                        button.classList.add('active');
                        document.getElementById(targetId).classList.remove('hidden');
                    });
                });

                // --- Inisialisasi Awal ---
                initializeMap();
                window.addEventListener('resize', () => {
                    if (map) {
                        map.invalidateSize(false);
                    }
                });
            });


            // --- Modal Functions ---
            const modalContainer = document.getElementById('modal-container');
            const modalContent = document.getElementById('modal-content');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Jika Anda butuh CSRF

            // Fungsi untuk menutup modal
            function closeModal(event) {
                // Hanya tutup jika klik di luar area konten modal
                if (event.target === modalContainer) {
                    modalContainer.classList.add('hidden');
                    modalContainer.classList.remove('flex');
                    modalContent.innerHTML = ''; // Kosongkan konten
                }
            }

            // Fungsi untuk membuka modal dan memuat daftar toko
            async function openManageStoresModal() {
                // Tampilkan loading spinner jika ada
                modalContent.innerHTML = '<div class="p-8 text-center">Memuat daftar toko...</div>';
                modalContainer.classList.remove('hidden');
                modalContainer.classList.add('flex');

                try {
                    // Panggil API dari StoreController@index
                    const response = await fetch('/api/stores', { // Sesuaikan URL jika perlu
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken // Kirim CSRF jika route API dilindungi olehnya
                        }
                    });

                    if (!response.ok) throw new Error('Gagal memuat data.');

                    const stores = await response.json();

                    // Bangun HTML dari data JSON yang diterima
                    let storesHtml = stores.map(store => `
                <div id="store-${store.id}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-600">
                    <div class="flex items-center space-x-4">
                        <img src="${store.image_url}" alt="${store.name}" class="w-16 h-16 object-cover rounded-md">
                        <div>
                            <p class="font-bold text-gray-800 dark:text-white">${store.name}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">${store.address}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="openStoreFormModal(${store.id})" class="p-2 text-blue-500 hover:text-blue-700" title="Edit Toko"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteStore(${store.id}, '${store.name}')" class="p-2 text-red-500 hover:text-red-700" title="Hapus Toko"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `).join('');

                    // Final HTML untuk modal
                    const modalHtml = `
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Toko Saya</h2>
                    <button onclick="openStoreFormModal()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-1"></i> Buat Toko Baru</button>
                </div>
                <div class="p-6 overflow-y-auto space-y-4">
                    ${stores.length > 0 ? storesHtml : '<div class="text-center py-10"><p class="text-gray-500">Anda belum memiliki toko.</p></div>'}
                </div>
            `;

                    modalContent.innerHTML = modalHtml;

                } catch (error) {
                    modalContent.innerHTML = `<div class="p-8 text-center text-red-500">Error: ${error.message}</div>`;
                }
            }

            // Fungsi untuk memuat form (Create/Edit) dari Blade Partial
            async function openStoreFormModal(storeId = null) {
                const url = storeId ? `/modals/store-form/${storeId}` : '/modals/store-form';
                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Gagal memuat form.');

                    const formHtml = await response.text();
                    modalContent.innerHTML = formHtml;
                    // Pastikan modal sudah terlihat jika belum
                    modalContainer.classList.remove('hidden');
                    modalContainer.classList.add('flex');
                } catch (error) {
                    modalContent.innerHTML = `<div class="p-8 text-center text-red-500">Error: ${error.message}</div>`;
                }
            }

            // Fungsi untuk menangani submit form (Create/Update)
            async function handleStoreFormSubmit(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);
                const storeId = form.dataset.storeId;

                const url = storeId ? `/api/stores/${storeId}` : '/api/stores';
                // Untuk update via form-data, kita POST dan tambahkan _method
                if (storeId) formData.append('_method', 'PATCH');

                try {
                    const response = await fetch(url, {
                        method: 'POST', // Selalu POST untuk FormData dengan file
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        // Tampilkan error validasi jika ada
                        if (result.errors) {
                            Object.keys(result.errors).forEach(key => {
                                const errorEl = form.querySelector(`#error-${key}`);
                                if (errorEl) errorEl.textContent = result.errors[key][0];
                            });
                        }
                        throw new Error(result.message || 'Terjadi kesalahan.');
                    }

                    // Jika berhasil, muat ulang daftar toko
                    alert(result.message); // Notifikasi sederhana
                    openManageStoresModal();

                } catch (error) {
                    alert(`Error: ${error.message}`);
                }
            }

            // Fungsi untuk menghapus toko
            async function deleteStore(storeId, storeName) {
                if (!confirm(`Apakah Anda yakin ingin menghapus toko "${storeName}"?`)) return;

                try {
                    const response = await fetch(`/api/stores/${storeId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message);

                    alert(result.message);
                    // Hapus elemen dari DOM tanpa perlu reload semua
                    document.getElementById(`store-${storeId}`).remove();
                } catch (error) {
                    alert(`Error: ${error.message}`);
                }
            }
        </script>
    @endpush

</x-map-layout>
