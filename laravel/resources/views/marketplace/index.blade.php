<form method="GET" action="{{ route('marketplace.index') }}">
    <button type="submit">Cari</button>
</form>

@foreach ($wasteVariants as $variant)
    <div>
        <h3>{{ $variant->waste->name }} - {{ $variant->volume_in_grams }}g</h3>
        <p>Toko: {{ $variant->waste->store->name }}</p>
        <p>Harga: Rp {{ number_format($variant->price, 2) }}</p>
        <p>Stok: {{ $variant->stock }}</p>
    </div>
@endforeach
{{ $wasteVariants->links() }} // Paginasi

<div id="mapid" style="height: 500px;"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script>
    var map = L.map('mapid').setView([-7.983908, 112.621391], 13); // Contoh: Malang
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    @foreach ($wasteVariants as $variant)
        @if ($variant->waste->store->latitude && $variant->waste->store->longitude)
            L.marker([{{ $variant->waste->store->latitude }}, {{ $variant->waste->store->longitude }}])
                .addTo(map)
                .bindPopup(
                    '<b>{{ Str::limit($variant->waste->name, 20) }} ({{ $variant->volume_in_grams }}g)</b><br>Toko: {{ $variant->waste->store->name }}<br>Harga: Rp {{ number_format($variant->price, 0) }}'
                    );
        @endif
    @endforeach

    // Jika user memberikan lokasi, tambahkan marker untuk user
    @if (request()->filled('user_latitude') && request()->filled('user_longitude'))
        L.marker([{{ request('user_latitude') }}, {{ request('user_longitude') }}], {
                icon: L.icon({
                    iconUrl: 'path/to/user_marker.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            })
            .addTo(map)
            .bindPopup('Lokasi Anda').openPopup();
        map.setView([{{ request('user_latitude') }}, {{ request('user_longitude') }}], 13);
    @endif
</script>
