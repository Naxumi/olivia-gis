<x-map-layout title="Peta & Info Pengelola Sampah (Simple)">

    {{-- Container Utama untuk Peta dan Panel (relative untuk positioning absolute child) --}}
    <div class="w-screen h-screen relative">

        <div id="mapContainer" class="w-full h-full bg-gray-300 dark:bg-gray-500">
            {{-- Peta akan diinisialisasi di sini oleh Leaflet --}}
        </div>

        {{-- Awalnya tersembunyi di luar layar kiri, kecuali di layar sm ke atas --}}
        <div id="sidePanel"
            class="absolute top-0 left-0 h-full z-30
                    w-full max-w-md sm:w-80 md:w-96 {{-- Lebar berbeda untuk responsivitas --}}
                    bg-white dark:bg-gray-800 shadow-2xl
                    transform -translate-x-full sm:translate-x-0 {{-- Default tersembunyi, kecuali sm+ --}}
                    transition-transform duration-300 ease-in-out
                    flex flex-col">

            <div class="p-2 text-right sm:hidden"> {{-- Hanya tampil di mobile saat panel terbuka --}}
                <button id="closePanelButtonMobile"
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">Informasi Detail</h2>
            </div>

            <div class="flex border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <button data-tab-target="detailBarang"
                    class="tab-button active flex-1 py-3 px-2 text-xs sm:text-sm text-center">
                    <i class="fas fa-recycle mr-1"></i> Info Daur Ulang
                </button>
                <button data-tab-target="profilToko" class="tab-button flex-1 py-3 px-2 text-xs sm:text-sm text-center">
                    <i class="fas fa-industry mr-1"></i> Profil Pengolah
                </button>
            </div>

            <div class="flex-grow overflow-y-auto custom-scrollbar p-3 sm:p-4 space-y-4">
                <div id="detailBarang" class="tab-content space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-700/70 p-3 rounded-lg shadow-md">
                        <div class="flex justify-center mb-2"><i class="fas fa-leaf text-4xl text-green-500"></i></div>
                        <h3 class="item-title text-md font-semibold text-green-600 text-center">Pupuk Organik Cair</h3>
                        <p class="item-id text-xs text-gray-400 text-center mb-2">ID: POC001</p>
                        <p class="text-xs text-gray-700 dark:text-gray-300">Deskripsi singkat produk daur ulang...</p>
                        <p class="text-xs text-gray-700 dark:text-gray-300 mt-1">Harga: Rp 25.000</p>
                    </div>
                </div>
                <div id="profilToko" class="tab-content hidden space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-700/70 p-3 rounded-lg shadow-md">
                        <div class="flex justify-center mb-2"><i class="fas fa-recycle text-4xl text-green-500"></i>
                        </div>
                        <h3 class="text-md font-semibold text-green-600 text-center">CV. Lestari Daur Ulang</h3>
                        <p class="text-xs text-gray-700 dark:text-gray-300">Alamat: Jl. Industri Hijau No. 8</p>
                    </div>
                </div>
            </div>
        </div>

        <button id="openPanelButton"
            class="fixed top-3 left-3 z-20 {{-- Di bawah panel jika panel terbuka --}}
                       sm:hidden p-2.5 bg-white dark:bg-gray-700 rounded-full shadow-lg text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-bars text-lg"></i>
        </button>

        {{-- Posisi diatur agar tidak tertimpa panel di desktop --}}
        <div id="searchBarContainer"
            class="absolute top-3 z-20
                    right-3 left-3 {{-- Mobile: mengisi ruang antar margin --}}
                    sm:left-auto {{-- Desktop: posisi kiri akan diatur berdasarkan panel --}}
                    sm:w-auto sm:max-w-sm md:max-w-md">
            <div class="relative">
                <input type="text" placeholder="Cari lokasi..."
                    class="w-full p-2.5 pl-9 text-xs sm:text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg focus:ring-green-500 focus:border-green-500 outline-none">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-xs sm:text-sm"></i>
                </div>
            </div>
        </div>

        <div class="absolute bottom-3 right-3 z-20 flex flex-col space-y-2">
            <button id="zoomInButton"
                class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700"><i
                    class="fas fa-plus"></i></button>
            <button id="zoomOutButton"
                class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700"><i
                    class="fas fa-minus"></i></button>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mapContainer = document.getElementById('mapContainer');
                const sidePanel = document.getElementById('sidePanel');
                const openPanelButton = document.getElementById('openPanelButton');
                const closePanelButtonMobile = document.getElementById('closePanelButtonMobile');
                const searchBarContainer = document.getElementById('searchBarContainer');
                let map = null;

                function initializeMap() {
                    if (!mapContainer) {
                        console.error("Map container not found!");
                        return;
                    }
                    if (map && typeof map.remove === 'function') {
                        map.remove();
                        map = null;
                    }
                    console.log('Initializing map. Container size:', mapContainer.offsetWidth, 'x', mapContainer
                        .offsetHeight);

                    map = L.map(mapContainer, {
                        zoomControl: false,
                        attributionControl: false
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        // attribution: '&copy; <a href="https://osm.org/copyright">OSM</a>'
                    }).addTo(map);

                    L.control.attribution({
                        prefix: '<a href="https://leafletjs.com">Leaflet</a> | &copy; <a href="https://osm.org/copyright">OSM</a>',
                        position: 'bottomright'
                    }).addTo(map);

                    requestAnimationFrame(() => {
                        map.setView([-6.2088, 106.8456], 11);
                        setTimeout(() => {
                            if (map) {
                                map.invalidateSize(true);
                                console.log('Map view set and invalidated.');
                            }
                        }, 200);
                    });

                    // Markers contoh
                    L.marker([-6.175110, 106.865036]).addTo(map).bindPopup(
                        '<b>Pengolah Sampah A</b><br><button class="show-details-btn" data-id="A001">Detail</button>'
                        );
                    L.marker([-6.200000, 106.800000]).addTo(map).bindPopup(
                        '<b>Bank Sampah B</b><br><button class="show-details-btn" data-id="B002">Detail</button>');

                    document.getElementById('zoomInButton').addEventListener('click', () => map.zoomIn());
                    document.getElementById('zoomOutButton').addEventListener('click', () => map.zoomOut());

                    map.on('popupopen', function(e) {
                        const detailButton = e.popup._contentNode.querySelector('.show-details-btn');
                        if (detailButton) {
                            detailButton.addEventListener('click', function() {
                                const itemId = this.getAttribute('data-id');
                                document.querySelector('#detailBarang .item-title').textContent =
                                    `Info untuk ID: ${itemId}`;
                                document.querySelector('.tab-button[data-tab-target="detailBarang"]')
                                    .click();
                                if (window.innerWidth < 640) openPanel();
                            });
                        }
                    });
                }


                function adjustSearchBarPosition(panelIsOpen) {
                    if (window.innerWidth >= 640) { // sm breakpoint
                        const panelWidth = sidePanel.offsetWidth; // Lebar aktual panel
                        if (panelIsOpen) {
                            // Geser search bar ke kanan panel
                            searchBarContainer.style.left = `${panelWidth + 20}px`; // 20px sebagai gap
                            searchBarContainer.style.right = '0.75rem'; // right-3
                            searchBarContainer.style.width = `calc(100% - ${panelWidth + 20 + 12}px)`; // 12px = right-3
                        } else {
                            // Panel tertutup di desktop, search bar bisa lebih ke kiri
                            searchBarContainer.style.left = '0.75rem'; // left-3
                            searchBarContainer.style.right = '0.75rem'; // right-3
                            searchBarContainer.style.width = 'auto';
                        }
                    } else { // Mobile
                        searchBarContainer.style.left = '0.75rem';
                        searchBarContainer.style.right = '0.75rem';
                        searchBarContainer.style.width = 'auto'; // Biarkan left & right menentukan lebar
                    }
                }

                function openPanel() {
                    sidePanel.classList.remove('-translate-x-full');
                    sidePanel.classList.add('translate-x-0');
                    openPanelButton.classList.add('hidden'); // Sembunyikan tombol open
                    adjustSearchBarPosition(true);
                    if (map) setTimeout(() => map.invalidateSize(true), 350); // Sesuaikan dengan durasi transisi
                }

                function closePanel() {
                    sidePanel.classList.remove('translate-x-0');
                    sidePanel.classList.add('-translate-x-full');
                    openPanelButton.classList.remove('hidden'); // Tampilkan tombol open
                    adjustSearchBarPosition(false);
                    if (map) setTimeout(() => map.invalidateSize(true), 350);
                }

                // Event Listeners Panel
                if (openPanelButton) openPanelButton.addEventListener('click', openPanel);
                if (closePanelButtonMobile) closePanelButtonMobile.addEventListener('click', closePanel);

                // Event Listeners Tab
                const tabButtons = document.querySelectorAll('.tab-button');
                const tabContents = document.querySelectorAll('.tab-content');
                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        tabButtons.forEach(btn => btn.classList.remove('active'));
                        tabContents.forEach(content => content.classList.add('hidden'));
                        button.classList.add('active');
                        document.getElementById(button.getAttribute('data-tab-target')).classList
                            .remove('hidden');
                    });
                });
                // Aktifkan tab pertama secara default
                if (tabButtons.length > 0) tabButtons[0].click();


                // Inisialisasi Peta
                initializeMap();

                // Pengaturan Awal Panel & Search Bar
                if (window.innerWidth >= 640) { // Desktop
                    sidePanel.classList.remove('-translate-x-full');
                    sidePanel.classList.add('translate-x-0');
                    openPanelButton.classList.add('hidden');
                    adjustSearchBarPosition(true);
                } else { // Mobile
                    sidePanel.classList.add('-translate-x-full');
                    sidePanel.classList.remove('translate-x-0');
                    openPanelButton.classList.remove('hidden');
                    adjustSearchBarPosition(false);
                }

                // Resize listener
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        const panelIsOpen = !sidePanel.classList.contains('-translate-x-full');
                        adjustSearchBarPosition(panelIsOpen);
                        if (map) map.invalidateSize();

                        // Re-evaluate panel visibility on resize for sm breakpoint
                        if (window.innerWidth >= 640) {
                            if (panelIsOpen) openPanelButton.classList.add('hidden');
                        } else {
                            if (!panelIsOpen) openPanelButton.classList.remove('hidden');
                        }
                    }, 250);
                });
            });
        </script>
    @endpush

</x-map-layout>
