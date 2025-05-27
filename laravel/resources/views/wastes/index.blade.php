
{{-- @extends('layouts.app') --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Limbah Interaktif</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

    <style>
        body { margin: 0; font-family: Arial, sans-serif; }
        #map-container {
            display: flex;
            height: 100vh; /* Tinggi penuh viewport */
            width: 100vw; /* Lebar penuh viewport */
        }
        #map {
            flex-grow: 1; /* Peta mengambil sisa ruang */
            height: 100%;
        }
        #waste-details-sidebar {
            width: 350px; /* Lebar sidebar, bisa disesuaikan */
            height: 100%;
            overflow-y: auto; /* Scroll jika konten panjang */
            padding: 20px;
            box-sizing: border-box;
            background-color: #f8f9fa;
            border-left: 1px solid #dee2e6;
            transition: transform 0.3s ease-in-out; /* Animasi halus */
            transform: translateX(100%); /* Sembunyikan di awal */
        }
        #waste-details-sidebar.visible {
            transform: translateX(0); /* Tampilkan sidebar */
        }
        #details-content h3 { margin-top: 0; }
        #close-sidebar-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 1.5em;
            background: none;
            border: none;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 5px;
        }
        .leaflet-popup-content {
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div id="map-container">
        <div id="map"></div>
        <div id="waste-details-sidebar">
            <button id="close-sidebar-btn" title="Tutup Detail">&times;</button>
            <h3>Detail Limbah</h3>
            <hr>
            <div id="details-content">
                <p>Klik marker di peta untuk melihat detail.</p>
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi peta dengan koordinat default dari controller
        const initialLat = {{ $defaultLocation['lat'] }};
        const initialLng = {{ $defaultLocation['lng'] }};
        const map = L.map('map').setView([initialLat, initialLng], 11); // Zoom level bisa disesuaikan

        // Tambahkan tile layer (misalnya OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const sidebar = document.getElementById('waste-details-sidebar');
        const detailsContent = document.getElementById('details-content');
        const closeBtn = document.getElementById('close-sidebar-btn');
        let activeMarker = null;
        const markers = {}; // Untuk menyimpan referensi marker

        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('visible');
            if (activeMarker) {
                // Mungkin reset style marker aktif jika ada
                activeMarker = null;
            }
        });

        async function fetchWasteDetails(wasteId) {
            detailsContent.innerHTML = '<p>Memuat detail...</p>';
            sidebar.classList.add('visible');

            try {
                const response = await fetch(`/api/wastes/${wasteId}/details`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const waste = await response.json();

                let html = `<h3>${waste.name || 'Nama Tidak Tersedia'}</h3>`;
                html += `<p><strong>ID:</strong> ${waste.id}</p>`;
                html += `<p><strong>Deskripsi:</strong> ${waste.description || 'Tidak ada deskripsi.'}</p>`;
                html += `<p><strong>Lokasi:</strong> Lat: ${waste.latitude}, Lng: ${waste.longitude}</p>`;

                // Tampilkan gambar jika ada (sesuaikan path dan storage link)
                if (waste.image_path) {
                    // Pastikan Anda sudah menjalankan `php artisan storage:link`
                    // dan image_path adalah path relatif dari direktori public/storage
                    html += `<img src="/storage/${waste.image_path}" alt="${waste.name}" style="width:100%; max-width:300px; margin-top:10px; border-radius:5px;">`;
                }

                if (waste.variants && waste.variants.length > 0) {
                    html += '<h4>Varian:</h4><ul>';
                    waste.variants.forEach(variant => {
                        html += `<li>${variant.name || 'Varian'} (${variant.attribute || 'Atribut tidak ada'})</li>`; // Sesuaikan field variant
                    });
                    html += '</ul>';
                }
                // Tambahkan detail lain yang relevan dari objek 'waste'

                detailsContent.innerHTML = html;
            } catch (error) {
                console.error("Tidak dapat mengambil detail limbah:", error);
                detailsContent.innerHTML = '<p>Gagal memuat detail limbah. Silakan coba lagi.</p>';
            }
        }

        async function loadWastesOnMap() {
            try {
                const response = await fetch('/api/wastes/map-data');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const wastes = await response.json();

                if (wastes.length === 0) {
                    console.log("Tidak ada data limbah untuk ditampilkan di peta.");
                    return;
                }

                const markerBounds = L.latLngBounds();

                wastes.forEach(waste => {
                    if (waste.latitude && waste.longitude) {
                        const marker = L.marker([waste.latitude, waste.longitude]).addTo(map);
                        marker.bindPopup(`<b>${waste.name}</b><br>Klik untuk detail.`);

                        marker.on('click', () => {
                            if (activeMarker) {
                                // Reset style marker sebelumnya jika perlu
                            }
                            activeMarker = marker;
                            // Highlight marker aktif jika perlu
                            fetchWasteDetails(waste.id);
                            map.setView([waste.latitude, waste.longitude], 15); // Pusatkan peta ke marker yang diklik
                        });
                        markers[waste.id] = marker; // Simpan marker
                        markerBounds.extend([waste.latitude, waste.longitude]);
                    }
                });

                // Sesuaikan view peta agar semua marker terlihat, jika ada marker
                if (markerBounds.isValid()) {
                    map.fitBounds(markerBounds.pad(0.1)); // pad(0.1) memberi sedikit padding
                }

            } catch (error) {
                console.error("Tidak dapat memuat data limbah di peta:", error);
                // Bisa tambahkan notifikasi error di halaman
            }
        }

        // Panggil fungsi untuk memuat data saat halaman siap
        document.addEventListener('DOMContentLoaded', loadWastesOnMap);

    </script>
</body>
</html>
