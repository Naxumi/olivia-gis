<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Interaktif - Eco Barter</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-green: #22c55e;
            /* Green-500 */
            --brand-green-dark: #16a34a;
            /* Green-600 */
        }

        body,
        html {
            font-family: 'Inter', system-ui, sans-serif;
            overflow: hidden;
        }

        .leaflet-pane {
            z-index: 10;
        }

        .leaflet-top,
        .leaflet-bottom {
            z-index: 20;
        }

        .leaflet-control-zoom {
            border: 1px solid #e5e7eb !important;
        }

        /* Panel Utama */
        #floating-panel {
            --panel-width-desktop: 400px;
            --panel-height-mobile: 75vh;
            transform: translateX(calc(-1 * var(--panel-width-desktop)));
            width: var(--panel-width-desktop);
            z-index: 30;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #floating-panel.is-open {
            transform: translateX(0);
        }

        /* Scrollbar Styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Tab Logic */
        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .tab-button.active {
            border-color: var(--brand-green);
            color: var(--brand-green);
        }

        .result-card.active {
            background-color: #ecfdf5;
            /* green-50 */
        }

        .dark .result-card.active {
            background-color: #052e16;
            /* green-950/50 */
        }

        /* Marker Kustom untuk Item Aktif */
        .leaflet-marker-icon.active-marker {
            filter: drop-shadow(0 0 8px var(--brand-green));
        }

        /* Pastikan style ini ada di dalam tag <style> di head Anda */
        .modal-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-overlay.open .modal-box {
            transform: translateY(0);
        }

        @media (max-width: 767px) {
            #floating-panel {
                width: 100%;
                height: var(--panel-height-mobile);
                top: auto;
                left: 0;
                right: 0;
                bottom: 0;
                transform: translateY(var(--panel-height-mobile));
                border-top-left-radius: 1.5rem;
                border-top-right-radius: 1.5rem;
                box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.1), 0 -8px 10px -6px rgba(0, 0, 0, 0.1);
            }

            #floating-panel.is-open {
                transform: translateY(0);
            }
        }

        /* Tambahkan ini di dalam tag <style> Anda */
        .accordion-item {
            border-bottom: 1px solid #e5e7eb;
            /* dark:border-gray-700 */
        }

        .dark .accordion-item {
            border-color: #374151;
        }

        .accordion-header {
            cursor: pointer;
            padding: 1rem 1.5rem;
            transition: background-color 0.2s ease;
        }

        .accordion-header:hover {
            background-color: #f9fafb;
            /* dark:bg-gray-800 */
        }

        .dark .accordion-header:hover {
            background-color: #1f2937;
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out, padding 0.3s ease;
            background-color: #f9fafb;
            /* dark:bg-gray-800/50 */
            padding: 0 1.5rem;
        }

        .dark .accordion-content {
            background-color: #1f2937;
        }

        .accordion-content.open {
            max-height: 1000px;
            /* Cukup besar untuk menampung konten form */
            padding: 1.5rem;
        }

        .accordion-header .icon {
            transition: transform 0.3s ease;
        }

        .accordion-header.open .icon {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

    <div id="map-container" class="relative w-screen h-screen">
        <div id="map" class="w-full h-full"></div>

        <div id="floating-panel"
            class="absolute top-0 left-0 h-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl shadow-2xl flex flex-col">
            <header class="p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-extrabold text-green-600 dark:text-green-400">Eco Barter</h1>
                    <button id="panel-close-btn"
                        class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 md:hidden"
                        aria-label="Tutup Panel">
                        <i class="fa-solid fa-times text-gray-600 dark:text-gray-400"></i>
                    </button>
                </div>
                <nav class="mt-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="-mb-px flex space-x-6">
                        <button class="tab-button py-3 px-1 border-b-2 font-semibold text-sm transition-colors"
                            data-tab="search-pane">Cari</button>
                        <button class="tab-button py-3 px-1 border-b-2 font-semibold text-sm transition-colors"
                            data-tab="detail-pane" disabled>Detail</button>
                        <button class="tab-button py-3 px-1 border-b-2 font-semibold text-sm transition-colors"
                            data-tab="profile-pane">Profil</button>
                    </div>
                </nav>
            </header>

            <main class="flex-grow overflow-y-auto custom-scrollbar">
                <div id="search-pane" class="tab-pane">
                    <form id="search-form"
                        class="p-4 space-y-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" id="q" name="q" placeholder="Cari nama sampah..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition">
                        <select id="sort_by" name="sort_by"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition">
                            <option value="">Urutkan: Relevansi</option>
                            <option value="nearby" disabled>Jarak Terdekat (aktifkan lokasi)</option>
                            <option value="price_asc">Harga Termurah</option>
                            <option value="rating_desc">Rating Tertinggi</option>
                        </select>
                    </form>
                    <div id="results-meta" class="px-4 pt-3 text-sm text-gray-500 dark:text-gray-400"></div>
                    <div id="search-results-list" class="divide-y divide-gray-200 dark:divide-gray-700"></div>
                </div>

                <div id="detail-pane" class="tab-pane"></div>

                <div id="profile-pane" class="tab-pane">
                    @auth
                        <div class="w-full">
                            <div class="accordion-item">
                            </div>

                            <div class="accordion-item">
                            </div>

                            @if (Auth::user()->hasRole('seller'))
                                <div class="accordion-item">
                                    <div class="accordion-header flex justify-between items-center">
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Toko Saya</h3>
                                        <i class="icon fa-solid fa-chevron-down text-gray-500"></i>
                                    </div>
                                    <div class="accordion-content">
                                        {{-- Memuat Komponen Livewire Daftar Toko --}}
                                        @livewire('user-stores')
                                    </div>
                                </div>
                            @endif

                            <div class="accordion-item">
                            </div>

                            <div class="p-6">
                            </div>
                        </div>
                    @else
                    @endauth
                </div>
            </main>
        </div>

        <div class="absolute top-4 left-4 z-20 md:hidden">
            <button id="panel-toggle-mobile"
                class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-800"
                aria-label="Buka Panel">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
        <div class="absolute bottom-4 right-4 z-20 space-y-3">
            <button id="get-location-btn" title="Cari Lokasi Saya"
                class="w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-location-crosshairs text-xl"></i>
            </button>
            <button id="recenter-map-btn" title="Pusatkan Peta ke Hasil"
                class="w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-compress-arrows-alt text-xl"></i>
            </button>
        </div>
    </div>

    {{-- [BARU] Struktur HTML untuk Modal Edit Profil --}}
    <div id="profile-edit-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 opacity-0 pointer-events-none z-50 transition-opacity duration-300">
        <div
            class="modal-box bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg transform -translate-y-5 transition-transform duration-300">
            <header class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Informasi Profil</h3>
                <button id="profile-modal-close-btn" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                    <i class="fa-solid fa-times text-gray-600 dark:text-gray-400"></i>
                </button>
            </header>
            <main id="profile-modal-content" class="p-6">
                {{-- Konten form akan dimasukkan oleh JavaScript di sini --}}
                <div class="text-center text-gray-500">Memuat form...</div>
            </main>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === State Aplikasi ===
            let userLocation = null;
            let currentResults = [];
            let activeWasteId = null;
            let debounceTimer;

            // === Inisialisasi Peta ===
            const leafletMap = L.map('map', {
                zoomControl: true,
                attributionControl: false
            }).setView([-2.5, 118.0], 5);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; CARTO'
            }).addTo(leafletMap);
            const markersLayer = L.featureGroup().addTo(leafletMap);

            // === DOM Elements ===
            const panel = document.getElementById('floating-panel');
            const mobilePanelToggle = document.getElementById('panel-toggle-mobile');
            const panelCloseBtn = document.getElementById('panel-close-btn');
            const searchForm = document.getElementById('search-form');
            const resultsMeta = document.getElementById('results-meta');
            const resultsList = document.getElementById('search-results-list');
            const detailPane = document.getElementById('detail-pane');
            const profilePane = document.getElementById('profile-pane');
            const getLocationBtn = document.getElementById('get-location-btn');
            const recenterMapBtn = document.getElementById('recenter-map-btn');

            // === Templating & Rendering (Fungsi untuk membuat elemen HTML) ===
            const formatPrice = (price) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(price);

            const renderSkeletonLoader = () => {
                const skeletonCard =
                    `<div class="p-4 animate-pulse"><div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-2"></div><div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/2 mb-3"></div><div class="flex justify-between items-center"><div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/4"></div><div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/4"></div></div></div>`;
                resultsList.innerHTML = Array(5).fill(skeletonCard).join('');
            };

            const renderSearchResults = (paginatedData) => {
                resultsList.innerHTML = '';
                resultsMeta.textContent = paginatedData.total > 0 ?
                    `Menampilkan ${paginatedData.from}-${paginatedData.to} dari ${paginatedData.total} hasil.` :
                    '';
                if (paginatedData.data.length === 0) {
                    resultsList.innerHTML =
                        `<div class="p-8 text-center text-gray-500 dark:text-gray-400">Tidak ada hasil ditemukan.</div>`;
                    markersLayer.clearLayers();
                    return;
                }
                markersLayer.clearLayers();
                paginatedData.data.forEach(item => {
                    const card = document.createElement('div');
                    card.className =
                        `result-card p-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200`;
                    card.dataset.id = item.waste_id;
                    card.innerHTML =
                        `<h3 class="font-bold text-gray-800 dark:text-white">${item.waste_name}</h3><p class="text-sm text-gray-600 dark:text-gray-400">${item.store_name}</p><div class="flex justify-between items-center mt-2 text-sm"><span class="font-semibold text-green-600">${formatPrice(item.price)}</span><span class="text-gray-500 flex items-center"><i class="fa-solid fa-star text-yellow-400 mr-1"></i> ${parseFloat(item.average_rating).toFixed(1)}</span></div>`;
                    card.addEventListener('click', () => showDetails(item.waste_id));
                    resultsList.appendChild(card);
                    if (item.latitude && item.longitude) {
                        const marker = L.marker([item.latitude, item.longitude], {
                            wasteId: item.waste_id
                        }).on('click', () => showDetails(item.waste_id));
                        markersLayer.addLayer(marker);
                    }
                });
                recenterMapToResults();
            };

            const renderDetailView = (item) => {
                detailPane.innerHTML =
                    `<div class="p-6"><button id="back-to-search-btn" class="mb-4 text-sm font-semibold text-green-600 hover:text-green-800 flex items-center"><i class="fa-solid fa-arrow-left mr-2"></i>Kembali ke Hasil</button><div class="space-y-4"><h2 class="text-2xl font-bold text-gray-900 dark:text-white">${item.waste_name}</h2><p class="text-md text-gray-600 dark:text-gray-300">Dijual oleh: <strong>${item.store_name}</strong></p><div class="p-4 bg-green-50 dark:bg-gray-800 rounded-lg space-y-3 text-sm"><div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Harga</span><strong class="text-lg text-green-700 dark:text-green-400">${formatPrice(item.price)}</strong></div><div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Stok Tersedia</span><strong class="dark:text-white">${item.stock}</strong></div><div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Rating Toko</span><strong class="dark:text-white flex items-center"><i class="fa-solid fa-star text-yellow-400 mr-1"></i> ${parseFloat(item.average_rating).toFixed(1)}</strong></div></div><button class="w-full mt-4 px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700">Hubungi Penjual</button></div></div>`;
                detailPane.querySelector('#back-to-search-btn').addEventListener('click', () => switchTab(
                    'search-pane'));
            };

            const renderProfileView = () => {
                profilePane.innerHTML =
                    `<div class="p-6"> @auth <div class="flex items-center space-x-4 mb-6"><div class="w-16 h-16 bg-green-500 text-white rounded-full flex items-center justify-center text-2xl font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div><div><h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</h3><p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p></div></div><div class="space-y-3"><a href="{{ route('profile.edit') }}" class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">Edit Profil</a><form method="POST" action="{{ route('logout') }}" class="w-full">@csrf<button type="submit" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">Logout</button></form></div> @else <div class="text-center py-8"><h3 class="text-lg font-bold text-gray-900 dark:text-white">Akses Penuh Fitur</h3><p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Silakan login atau mendaftar untuk mengelola profil Anda.</p><div class="mt-6 flex justify-center gap-4"><a href="{{ route('login') }}" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">Login</a><a href="{{ route('register') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">Register</a></div></div> @endauth </div>`;
            };

            // === Fungsi Inti ===
            const performSearch = async () => {
                const params = new URLSearchParams(new FormData(searchForm));
                if (userLocation && params.get('sort_by') === 'nearby') {
                    params.append('latitude', userLocation.lat);
                    params.append('longitude', userLocation.lng);
                }
                try {
                    const response = await fetch(`{{ route('api.wastes.search') }}?${params.toString()}`);
                    const results = await response.json();
                    currentResults = results.data;
                    renderSearchResults(results);
                } catch (error) {
                    resultsList.innerHTML =
                        `<div class="p-8 text-center text-gray-500">Gagal memuat data.</div>`;
                    console.error("Search Error:", error);
                }
            };
            const debouncedSearch = debounce(performSearch, 400);

            const showDetails = (wasteId) => {
                const item = currentResults.find(r => r.waste_id === wasteId);
                if (!item) return;
                activeWasteId = wasteId;
                renderDetailView(item);
                switchTab('detail-pane');
                markersLayer.eachLayer(marker => {
                    const icon = marker.getIcon();
                    if (marker.options.wasteId === wasteId) {
                        leafletMap.flyTo(marker.getLatLng(), 15);
                        icon.options.className += ' active-marker';
                    } else {
                        icon.options.className = icon.options.className.replace(' active-marker', '');
                    }
                    marker.setIcon(icon);
                });
                document.querySelectorAll('.result-card').forEach(c => c.classList.toggle('active', c.dataset
                    .id == wasteId));
            };

            const recenterMapToResults = () => {
                const bounds = markersLayer.getBounds();
                if (bounds.isValid()) {
                    leafletMap.flyToBounds(bounds, {
                        padding: [50, 50],
                        maxZoom: 15
                    });
                }
            };

            const findUserLocation = () => leafletMap.locate({
                setView: true,
                maxZoom: 15
            });

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // === UI & Event Listeners ===
            const togglePanel = (forceOpen = null) => {
                const isOpen = panel.classList.contains('is-open');
                if (forceOpen === true || (forceOpen === null && !isOpen)) panel.classList.add('is-open');
                else panel.classList.remove('is-open');
                setTimeout(() => leafletMap.invalidateSize(), 400);
            };

            const switchTab = (targetTabId) => {
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
                document.getElementById(targetTabId).classList.add('active');
                document.querySelector(`button[data-tab="${targetTabId}"]`).classList.add('active');
                document.querySelector(`button[data-tab="detail-pane"]`).disabled = (targetTabId !==
                    'detail-pane');
            };

            mobilePanelToggle.addEventListener('click', () => togglePanel());
            panelCloseBtn.addEventListener('click', () => togglePanel(false));
            document.querySelectorAll('.tab-button').forEach(b => b.addEventListener('click', (e) => switchTab(e
                .currentTarget.dataset.tab)));
            searchForm.addEventListener('input', debouncedSearch);
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                performSearch();
            });
            getLocationBtn.addEventListener('click', findUserLocation);
            recenterMapBtn.addEventListener('click', recenterMapToResults);

            leafletMap.on('locationfound', (e) => {
                userLocation = e.latlng;
                L.marker(userLocation, {
                    icon: L.divIcon({
                        className: 'p-2 bg-blue-500 rounded-full border-4 border-white shadow-lg'
                    })
                }).addTo(leafletMap);
                document.querySelector('#sort_by option[value="nearby"]').disabled = false;
            });
            leafletMap.on('locationerror', (e) => alert("Tidak dapat menemukan lokasi Anda: " + e.message));

            // === Inisiasi Aplikasi ===
            switchTab('search-pane');
            renderProfileView();
            performSearch();
            if (window.innerWidth >= 768) togglePanel(true);
        });

        // Tambahkan ini di dalam event listener DOMContentLoaded
        document.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;

                // Tutup semua accordion lain
                document.querySelectorAll('.accordion-content.open').forEach(openContent => {
                    if (openContent !== content) {
                        openContent.classList.remove('open');
                        openContent.previousElementSibling.classList.remove('open');
                    }
                });

                // Toggle accordion yang diklik
                header.classList.toggle('open');
                content.classList.toggle('open');
            });
        });
    </script>
</body>

</html>
