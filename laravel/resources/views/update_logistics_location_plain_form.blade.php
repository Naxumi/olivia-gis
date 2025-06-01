<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Lokasi Logistik #{{ $logisticsItem->id }} (Form Biasa)</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Update Lokasi untuk Pengiriman #{{ $logisticsItem->id }}</h1>
        <p>Status Saat Ini: <strong>{{ $logisticsItem->status }}</strong></p>

        {{-- Menampilkan pesan error dari validasi Laravel atau session flash --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success_message'))
            <div class="alert alert-success">
                {{ session('success_message') }}
            </div>
        @endif
        @if (session('error_message'))
            <div class="alert alert-danger">
                {{ session('error_message') }}
            </div>
        @endif


        @if ($logisticsItem->status === \App\Models\Logistics::STATUS_IN_TRANSIT)
            <form action="{{ route('api.logistics.updateLocation', ['logistics' => $logisticsItem->id]) }}"
                method="POST">
                @csrf {{-- Wajib untuk proteksi CSRF --}}
                @method('PATCH') {{-- Method Spoofing untuk mengirim sebagai PATCH --}}

                <div>
                    <label for="current_latitude">Latitude Saat Ini:</label>
                    <input type="number" step="any" id="current_latitude" name="current_latitude"
                        value="{{ old('current_latitude') }}" required>
                </div>
                <div>
                    <label for="current_longitude">Longitude Saat Ini:</label>
                    <input type="number" step="any" id="current_longitude" name="current_longitude"
                        value="{{ old('current_longitude') }}" required>
                </div>
                <button type="submit">Update Lokasi</button>
            </form>
        @else
            <p class="alert alert-danger">Lokasi tidak dapat diupdate karena status pengiriman bukan "IN_TRANSIT".</p>
        @endif
    </div>
</body>

</html>
