<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace Limbah</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Reset dan Global Styles */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        * {
            box-sizing: border-box;
        }

        /* Layout Utama */
        .marketplace-container {
            display: flex;
            height: 100vh; /* Tinggi penuh viewport */
            width: 100%;
            overflow: hidden; /* Mencegah scroll di body */
        }

        .sidebar {
            width: 380px; /* Lebar sidebar yang lebih representatif */
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e0e0e0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }

        .sidebar-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .sidebar-header h2 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .search-form {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .search-form input[type="text"] {
            width: 100%;
            padding: 12px 18px;
            border: 1px solid #ced4da;
            border-radius: 25px;
            font-size: 0.95rem;
            margin-bottom: 10px; /* Jarak jika ada elemen lain di bawahnya */
        }
        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #4285F4;
            box-shadow: 0 0 0 0.2rem rgba(66, 133, 244, 0.25);
        }

        .search-form button {
            width: 100%;
            padding: 12px 18px;
            background-color: #4285F4;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .search-form button:hover {
            background-color: #357ae8;
        }

        .waste-list-container {
            flex-grow: 1; /* Mengambil sisa ruang di sidebar */
            overflow-y: auto; /* Scroll jika konten lebih panjang */
            padding: 15px 5px 15px 20px; /* Sedikit padding kanan dikurangi untuk scrollbar */
        }

        /* Styling Kartu Limbah */
        .waste-card {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 15px;
            margin-right: 15px; /* Jarak untuk scrollbar */
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: box-shadow 0.25s ease, transform 0.25s ease;
        }

        .waste-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .waste-card h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: #343a40;
            margin-top: 0;
            margin-bottom: 6px;
        }

        .waste-card .store-name,
        .waste-card .stock {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 8px;
        }
        .waste-card .price {
            font-size: 1.05rem;
            font-weight: 700;
            color: #007bff; /* Warna aksen untuk harga */
            margin-bottom: 0;
        }

        /* Area Peta */
        .map-area {
            flex-grow: 1;
            height: 100%; /* Peta mengisi seluruh area */
        }

        #mapid {
            height: 100%;
            width: 100%;
            background-color: #e0e0e0; /* Warna fallback jika tile lama dimuat */
        }

        /* Styling Popup Leaflet */
        .leaflet-popup-content-wrapper {
            border-radius: 8px !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15) !important;
        }
        .leaflet-popup-content {
            font-family: 'Inter', sans-serif !important;
            padding: 12px !important;
            font-size: 0.95rem !important;
            line-height: 1.6 !important;
        }
        .leaflet-popup-content b {
            color: #333;
            font-size: 1.05em;
            display: block;
            margin-bottom: 4px;
        }
        .leaflet-popup-close-button {
            padding: 8px 8px 0 0 !important;
            color: #777 !important;
        }

        /* Styling Paginasi (Contoh dasar untuk output default Laravel) */
        .pagination-container {
            padding: 15px 20px;
            border-top: 1px solid #e0e0e0;
        }
        .pagination {
            display: flex;
            justify-content: center;
            padding-left: 0;
            list-style: none;
            margin: 0;
        }
        .pagination li {
            margin: 0 4px;
        }
        .pagination li a,
        .pagination li span {
            display: block;
            padding: 8px 14px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #dee2e6;
            border-radius: 20px; /* Sudut melengkung */
            transition: background-color 0.2s, color 0.2s;
            font-size: 0.9rem;
        }
        .pagination li.active span,
        .pagination li.active a { /* Tambahkan 'a' jika paginator menggunakan <a> untuk halaman aktif */
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .pagination li.disabled span {
            color: #6c757d;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .pagination li a:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }
        /* Sembunyikan label prev/next bawaan jika menggunakan ikon */
        .pagination .page-link span[aria-hidden="true"],
        .pagination .page-link span.sr-only {
             /* Jika Anda ingin mengganti "Previous", "Next" dengan ikon */
        }

        /* Media query sederhana untuk mobile (opsional, kembangkan lebih lanjut) */
        @media (max-width: 768px) {
            .marketplace-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: 40vh; /* Sidebar mengambil sebagian tinggi */
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .map-area {
                height: 60vh; /* Peta mengambil sisa tinggi */
            }
            .search-form input[type="text"] {
                font-size: 0.9rem;
            }
             .search-form button {
                font-size: 0.95rem;
            }
            .waste-card h3 {
                font-size: 1.05rem;
            }
        }

    </style>
</head>
<body>

    <div class="marketplace-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Marketplace</h2>
            </div>

            <form method="GET" action="{{ route('marketplace.index') }}" class="search-form">
                {{-- CSRF tidak diperlukan untuk GET form --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari limbah, toko...">
                {{-- Input untuk latitude dan longitude pengguna bisa ditambahkan di sini jika diperlukan --}}
                {{-- <input type="hidden" name="user_latitude" value="{{ request('user_latitude') }}"> --}}
                {{-- <input type="hidden" name="user_longitude" value="{{ request('user_longitude') }}"> --}}
                <button type="submit">Cari</button>
            </form>

            <div class="waste-list-container">
                @if ($wasteVariants->count() > 0)
                    @foreach ($wasteVariants as $variant)
                        <div class="waste-card"
                             data-id="{{ $variant->id }}"
                             data-name="{{ Str::limit($variant->waste->name, 25) }} ({{ $variant->volume_in_grams }}g)"
                             data-store="{{ $variant->waste->store->name }}"
                             data-price="Rp {{ number_format($variant->price, 0, ',', '.') }}"
                             data-latitude="{{ $variant->waste->store->latitude }}"
                             data-longitude="{{ $variant->waste->store->longitude }}">
                            <h3>{{ $variant->waste->name }} - {{ $variant->volume_in_grams }}g</h3>
                            <p class="store-name">Toko: {{ $variant->waste->store->name }}</p>
                            <p class="price">Harga: Rp {{ number_format($variant->price, 0, ',', '.') }}</p>
                            <p class="stock">Stok: {{ $variant->stock }}</p>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; padding: 20px; color: #6c757d;">Tidak ada data untuk ditampilkan.</p>
                @endif
            </div>

            @if ($wasteVariants->hasPages())
            <div class="pagination-container">
                {{ $wasteVariants->links() }} {{-- Paginasi --}}
            </div>
            @endif
        </aside>

        <main class="map-area">
            <div id="mapid"></div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Inisialisasi Peta
        // Default view (Contoh: Malang), akan di-override jika ada lokasi pengguna
        var map = L.map('mapid').setView([-7.983908, 112.621391], 13);
        var markersLayer = L.layerGroup().addTo(map); // Layer untuk marker agar mudah di-clear

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Fungsi untuk membuat popup content
        function createPopupContent(variantName, storeName, price) {
            return `<b>${variantName}</b><br>Toko: ${storeName}<br>Harga: ${price}`;
        }

        // Menambahkan marker untuk setiap varian limbah
        @if ($wasteVariants->count() > 0)
            @foreach ($wasteVariants as $variant)
                @if ($variant->waste->store->latitude && $variant->waste->store->longitude)
                    var markerLat = {{ $variant->waste->store->latitude }};
                    var markerLon = {{ $variant->waste->store->longitude }};
                    var variantName = `{{ Str::limit($variant->waste->name, 25) }} ({{ $variant->volume_in_grams }}g)`;
                    var storeName = `{{ Str::replace("'", "\\'", $variant->waste->store->name) }}`; // Escape single quotes for JS
                    var price = `Rp {{ number_format($variant->price, 0, ',', '.') }}`;

                    var marker = L.marker([markerLat, markerLon])
                        .addTo(markersLayer)
                        .bindPopup(createPopupContent(variantName, storeName, price));

                    // Simpan referensi marker jika perlu (misalnya, untuk interaksi klik kartu)
                    marker.wasteVariantId = {{ $variant->id }};
                @endif
            @endforeach
        @endif

        // Menambahkan marker untuk lokasi pengguna jika ada
        @if (request()->filled('user_latitude') && request()->filled('user_longitude'))
            var userLat = {{ request('user_latitude') }};
            var userLon = {{ request('user_longitude') }};

            // Ganti 'path/to/user_marker.png' dengan path ke ikon marker Anda yang valid
            // Jika tidak ada ikon custom, Anda bisa menggunakan marker default Leaflet atau L.circleMarker
            var userIcon = L.icon({
                iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png', // Contoh default marker
                // iconUrl: 'path/to/user_marker.png', // GANTI INI JIKA PUNYA IKON CUSTOM
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                shadowSize: [41, 41]
            });

            L.marker([userLat, userLon], { icon: userIcon })
                .addTo(map)
                .bindPopup('Lokasi Anda Saat Ini')
                .openPopup();
            map.setView([userLat, userLon], 14); // Pusatkan peta ke lokasi pengguna
        @endif


        // Interaksi: Klik kartu di sidebar akan mengarahkan peta dan membuka popup
        document.querySelectorAll('.waste-card').forEach(card => {
            card.addEventListener('click', function() {
                const lat = parseFloat(this.dataset.latitude);
                const lon = parseFloat(this.dataset.longitude);
                const variantId = parseInt(this.dataset.id);

                if (!isNaN(lat) && !isNaN(lon)) {
                    map.setView([lat, lon], 15);

                    // Cari marker yang sesuai dan buka popupnya
                    markersLayer.eachLayer(function(layer) {
                        if (layer instanceof L.Marker && layer.wasteVariantId === variantId) {
                            layer.openPopup();
                        }
                    });
                }
            });
        });

    </script>
</body>
</html>