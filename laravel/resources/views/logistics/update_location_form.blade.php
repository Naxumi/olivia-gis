<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Lokasi Pengiriman #{{ $logisticsItem->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7f6;
            color: #333;
        }

        .container {
            max-width: 550px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8em;
        }

        p {
            line-height: 1.65;
            color: #555;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 7px;
            font-weight: 600;
            color: #444;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        input[type="number"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            display: block;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid transparent;
            font-size: 0.95em;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border-color: #badbcc;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border-color: #f5c2c7;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .info-box {
            background-color: #e2f3f5;
            border-left: 5px solid #007bff;
            margin-bottom: 25px;
            padding: 12px 18px;
            border-radius: 5px;
        }

        .info-box p {
            margin: 5px 0;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Update Lokasi Pengiriman #{{ $logisticsItem->id }}</h1>

        <div class="info-box">
            <p><strong>ID Transaksi:</strong> {{ $logisticsItem->transaction_id }}</p>
            <p><strong>Status Saat Ini:</strong>
                <span
                    style="font-weight: bold; color: {{ $logisticsItem->status === \App\Models\Logistics::STATUS_IN_TRANSIT ? '#28a745' : '#dc3545' }};">
                    {{ Str::title(str_replace('_', ' ', $logisticsItem->status)) }}
                </span>
            </p>
            @if ($logisticsItem->current_location)
                <p><strong>Lokasi Terakhir:</strong> Lat: {{ $logisticsItem->current_location->getLatitude() }}, Lon:
                    {{ $logisticsItem->current_location->getLongitude() }}
                    <br>(Diperbarui:
                    {{ $logisticsItem->last_updated_at ? $logisticsItem->last_updated_at->format('d M Y, H:i') : 'N/A' }})
                </p>
            @endif
            @if ($logisticsItem->estimated_delivery_time)
                <p><strong>Estimasi Tiba:</strong> {{ $logisticsItem->estimated_delivery_time->format('d M Y, H:i') }}
                </p>
            @endif
        </div>

        {{-- Menampilkan pesan error validasi Laravel --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Oops! Ada beberapa masalah dengan input Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Menampilkan pesan sukses atau error dari session flash --}}
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
            {{-- Menggunakan nama rute dari web.php jika Anda memindahkannya --}}
            <form action="{{ route('api.logistics.updateLocation', ['logistics' => $logisticsItem->id]) }}"
                method="POST">
                @csrf
                @method('PATCH')

                <div>
                    <label for="current_latitude">Latitude Saat Ini:</label>
                    <input type="number" step="any" id="current_latitude" name="current_latitude"
                        value="{{ old('current_latitude') }}" required placeholder="Contoh: -6.200000">
                </div>

                <div>
                    <label for="current_longitude">Longitude Saat Ini:</label>
                    <input type="number" step="any" id="current_longitude" name="current_longitude"
                        value="{{ old('current_longitude') }}" required placeholder="Contoh: 106.816666">
                </div>

                <button type="submit">Update Lokasi & Estimasi</button>
            </form>
        @else
            <div class="alert alert-danger">
                Lokasi tidak dapat diupdate karena status pengiriman bukan "IN TRANSIT".
            </div>
        @endif

        {{-- <a href="{{ url('/dashboard') }}" class="back-link">Kembali ke Dashboard</a> --}}
        {{-- Ganti '/dashboard' dengan URL atau nama rute dashboard Anda yang sebenarnya --}}
    </div>
</body>

</html>
