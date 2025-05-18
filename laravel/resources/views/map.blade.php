<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mapping | Akomodasi</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            font-family: sans-serif;
        }

        .sidebar {
            width: 300px;
            background-color: #f4f4f4;
            padding: 20px;
            border-right: 1px solid #ccc;
        }

        .sidebar h2 {
            margin-top: 0;
        }

        .category-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .category-btn:hover {
            background-color: #e2e8f0;
        }

        .map-container {
            flex: 1;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .bottom-tab {
            position: fixed;
            bottom: 0;
            left: 340px;
            /* agar tidak menutupi sidebar */
            right: 0;
            background: white;
            border-top: 1px solid #ccc;
            padding: 10px;
            display: flex;
            justify-content: center;
            gap: 20px;
            z-index: 999;
        }

        .tab-btn {
            padding: 10px 20px;
            background-color: #3182ce;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .tab-btn:hover {
            background-color: #2b6cb0;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Pilih kategori</h2>
        <div class="category-btn">
            <div style="width:20px;height:20px;background:#ccc;"></div> Bahan Mentah
        </div>
        <div class="category-btn">
            <div style="width:20px;height:20px;background:#ccc;"></div> Limbah
        </div>
        <div class="category-btn">
            <div style="width:20px;height:20px;background:#ccc;"></div> Kerajinan
        </div>
    </div>

    <div class="map-container">
        <div id="map"></div>
    </div>

    <div class="bottom-tab">
        <button class="tab-btn">Lacak Posisi</button>
        <button class="tab-btn">Reset Peta</button>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        //style

        var map = L.map('map').setView([-7.9519, 112.6149], 15); // Malang, Lembah Dieng

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([-7.9519, 112.6149]).addTo(map)
            .bindPopup("Kos Hapis")
            .openPopup();

        //Fungsi untuk melacak posisi pengguna
        document.querySelector(".tab-btn:nth-child(1)").addEventListener("click", () => {
            map.locate({
                setView: true,
                maxZoom: 17
            });
        });

        document.querySelector(".tab-btn:nth-child(2)").addEventListener("click", () => {
            map.setView([-7.9519, 112.6149], 15); // Reset ke Lembah Dieng
        });
    </script>
</body>

</html>
