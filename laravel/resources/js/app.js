import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { initializeMap } from './map-initializer.js';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('map')) {
        initializeMap('map');
    }
});

// import 'leaflet/dist/leaflet.css';
