@extends('layouts.test-map-layout')

@section('content')
    <div id="minimalMap"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM Loaded. Initializing minimal map...');
        const mapContainer = document.getElementById('minimalMap');

        if (mapContainer) {
            console.log('Map container #minimalMap found.');
            // Beri tinggi eksplisit jika style di layout tidak langsung terbaca oleh JS saat init
            if (mapContainer.offsetHeight === 0) {
                 console.warn('#minimalMap height is 0! Setting to 400px for test.');
                 mapContainer.style.height = '400px'; // Tinggi darurat
            }

            try {
                var map = L.map('minimalMap').setView([-6.2088, 106.8456], 11);
                console.log('Leaflet map object created.');

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                console.log('Tile layer added.');

                // Penting: InvalidateSize setelah tile layer ditambahkan dan DOM stabil
                setTimeout(function() {
                    map.invalidateSize(true);
                    console.log('Minimal map invalidateSize called.');
                }, 100); // Sedikit jeda

                L.marker([-6.2088, 106.8456]).addTo(map)
                    .bindPopup('Test Marker Jakarta.')
                    .openPopup();
                console.log('Test marker added.');

            } catch (e) {
                console.error('Error during minimal map initialization:', e);
            }

        } else {
            console.error('#minimalMap container NOT found!');
        }
    });
</script>
@endpush