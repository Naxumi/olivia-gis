<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Peta Interaktif - NamaPlatformAnda' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBh_wpLojA9YYxUVoPAYoM/BEMPg1VlBsnPHY="
     crossorigin=""/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        #mapContainer { /* Pastikan ini tidak bentrok dengan Tailwind h-full w-full */ }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #e5e7eb; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }
        .dark .custom-scrollbar::-webkit-scrollbar-track { background: #374151; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #374151; }

        .tab-button {
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .tab-button.active {
            border-bottom-width: 3px;
            border-color: rgb(34 197 94); /* green-500 */
            color: rgb(34 197 94); /* green-500 */
            font-weight: 600;
            background-color: rgba(52, 211, 153, 0.05);
        }
        .dark .tab-button.active {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: rgb(52 211 153); /* green-400 */
            color: rgb(52 211 153); /* green-400 */
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-200 dark:bg-gray-900">
    <div class="relative h-screen w-screen flex overflow-hidden">
        {{ $slot }}
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    @stack('scripts')
</body>
</html>