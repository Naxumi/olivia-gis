// resources/js/map-initializer.js
// import L from 'leaflet';

export function initializeMap(containerId) {
    const leafletMap = L.map(containerId, { zoomControl: false, attributionControl: false }).setView([-2.5, 118.0], 5);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO'
    }).addTo(leafletMap);

    const markersLayer = L.featureGroup().addTo(leafletMap);

    // Dengarkan event dari Livewire untuk update marker
    Livewire.on('wastesUpdated', (event) => {
        markersLayer.clearLayers();
        if (!event.wastes || event.wastes.length === 0) return;

        event.wastes.forEach(item => {
            if (item.latitude && item.longitude) {
                const marker = L.marker([item.latitude, item.longitude])
                    .on('click', () => {
                        // Kirim event ke Livewire saat marker diklik
                        Livewire.dispatch('showDetailsFromMap', { wasteId: item.waste_id });
                    });
                markersLayer.addLayer(marker);
            }
        });

        // Auto-zoom ke hasil pencarian
        const bounds = markersLayer.getBounds();
        if (bounds.isValid()) {
            leafletMap.flyToBounds(bounds, { padding: [50, 50], maxZoom: 15 });
        }
    });

    return leafletMap;
}