<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Halaman Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        h2,
        h3 {
            color: #555;
        }

        form {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .user-info {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <h1>🧪 Test Fungsi Transaksi</h1>

    @if (Auth::check())
        <div class="user-info">
            <p>Login sebagai: <strong>{{ Auth::user()->name }}</strong> (ID: {{ Auth::id() }})</p>
            <p>Roles:
                @if (Auth::user()->roles->isNotEmpty())
                    @foreach (Auth::user()->roles as $role)
                        <span
                            style="background-color: #007bff; color:white; padding: 2px 5px; border-radius:3px; font-size:0.9em;">{{ $role->name }}</span>
                    @endforeach
                @else
                    <span>Tidak ada peran</span>
                @endif
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background-color:#dc3545;">Logout</button>
            </form>
        </div>
    @else
        <p><a href="{{ route('login') }}">Login dulu</a> untuk menguji.</p>
    @endif

    @if (Auth::check())
        <div class="container">
            <h2>1. Buat Transaksi Baru (sebagai Buyer)</h2>
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <label for="waste_variant_id_store">ID Varian Limbah:</label>
                <select name="waste_variant_id" id="waste_variant_id_store" required>
                    <option value="">Pilih Varian Limbah</option>
                    @foreach ($waste_variants as $variant)
                        <option value="{{ $variant->id }}">ID: {{ $variant->id }} (Stok: {{ $variant->stock }}, Harga:
                            {{ $variant->price }}) - Milik Waste ID: {{ $variant->waste_id }}</option>
                    @endforeach
                </select>

                <label for="quantity_store">Jumlah:</label>
                <input type="number" id="quantity_store" name="quantity" value="1" min="1" required>

                <label for="payment_method_store">Metode Pembayaran:</label>
                <select name="payment_method" id="payment_method_store" required>
                    <option value="cod">COD (Cash on Delivery)</option>
                    <option value="bank_transfer_bca">Transfer Bank BCA</option>
                    <option value="dll">DLL (Metode Lain)</option>
                </select>
                <button type="submit">Buat Transaksi</button>
            </form>
        </div>

        <div class="container">
            <h2>2. Konfirmasi Transaksi (sebagai Seller/Admin)</h2>
            <p>Pilih ID transaksi yang masih 'pending'.</p>
            <form action="" method="POST" id="confirmForm">
                @csrf
                @method('PATCH')
                <label for="transaction_id_confirm">ID Transaksi (Pending):</label>
                <select name="transaction_id_confirm_select" id="transaction_id_confirm_select"
                    onchange="document.getElementById('confirmForm').action = '/transactions/' + this.value + '/confirm-by-seller';">
                    <option value="">Pilih Transaksi Pending</option>
                    @foreach ($transactions_pending as $transaction)
                        <option value="{{ $transaction->id }}">ID: {{ $transaction->id }} (Buyer:
                            {{ $transaction->buyer_id }}, Seller: {{ $transaction->seller_id }})</option>
                    @endforeach
                </select>

                <label for="distributor_id_confirm">(Opsional) Assign ID Distributor:</label>
                <select name="distributor_id" id="distributor_id_confirm">
                    <option value="">Tidak Assign Distributor</option>
                    @foreach ($users as $user)
                        @if ($user->hasRole('distributor'))
                            {{-- Pastikan user model punya method hasRole --}}
                            <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})
                            </option>
                        @endif
                    @endforeach
                </select>
                <button type="submit">Konfirmasi Transaksi</button>
            </form>
        </div>

        <div class="container">
            <h2>3. Tandai Sudah Diambil (sebagai Distributor)</h2>
            <p>Pilih ID transaksi yang statusnya 'confirmed'.</p>
            <form action="" method="POST" id="pickupForm">
                @csrf
                @method('PATCH')
                <label for="transaction_id_pickup">ID Transaksi (Confirmed):</label>
                <select name="transaction_id_pickup_select" id="transaction_id_pickup_select"
                    onchange="document.getElementById('pickupForm').action = '/transactions/' + this.value + '/pickup-by-distributor';">
                    <option value="">Pilih Transaksi Confirmed</option>
                    @foreach ($transactions_confirmed as $transaction)
                        <option value="{{ $transaction->id }}">ID: {{ $transaction->id }} (Logistics ID:
                            {{ $transaction->logistics_id ?? 'N/A' }})</option>
                    @endforeach
                </select>
                <button type="submit">Tandai Sudah Diambil</button>
            </form>
        </div>

        <div class="container">
            <h2>4. Tandai Sudah Terkirim (sebagai Distributor)</h2>
            <p>Pilih ID transaksi yang statusnya 'picked_up'.</p>
            <form action="" method="POST" id="deliverForm">
                @csrf
                @method('PATCH')
                <label for="transaction_id_deliver">ID Transaksi (Picked Up):</label>
                <select name="transaction_id_deliver_select" id="transaction_id_deliver_select"
                    onchange="document.getElementById('deliverForm').action = '/transactions/' + this.value + '/deliver-by-distributor';">
                    <option value="">Pilih Transaksi Picked Up</option>
                    @foreach ($transactions_picked_up as $transaction)
                        <option value="{{ $transaction->id }}">ID: {{ $transaction->id }}</option>
                    @endforeach
                </select>
                <button type="submit">Tandai Sudah Terkirim</button>
            </form>
        </div>

        <div class="container">
            <h2>5. Batalkan Transaksi</h2>
            <p>Pilih ID transaksi yang ingin dibatalkan (misalnya yang 'pending' atau 'confirmed').</p>
            <form action="" method="POST" id="cancelForm">
                @csrf
                @method('PATCH')
                <label for="transaction_id_cancel">ID Transaksi:</label>
                <input type="number" id="transaction_id_cancel_input" name="transaction_id_cancel_input_direct"
                    placeholder="Masukkan ID Transaksi">
                <select name="transaction_id_cancel_select" id="transaction_id_cancel_select"
                    onchange="document.getElementById('cancelForm').action = '/transactions/' + this.value + '/cancel'; document.getElementById('transaction_id_cancel_input').value = this.value;">
                    <option value="">Pilih Transaksi untuk Dibatalkan</option>
                    @foreach ($transactions_pending as $transaction)
                        <option value="{{ $transaction->id }}">Pending - ID: {{ $transaction->id }}</option>
                    @endforeach
                    @foreach ($transactions_confirmed as $transaction)
                        <option value="{{ $transaction->id }}">Confirmed - ID: {{ $transaction->id }}</option>
                    @endforeach
                    @foreach ($transactions_picked_up as $transaction)
                        <option value="{{ $transaction->id }}">Picked Up - ID: {{ $transaction->id }}</option>
                    @endforeach
                </select>
                <label for="reason_cancel">Alasan Pembatalan (opsional):</label>
                <textarea id="reason_cancel" name="reason"></textarea>
                <button type="submit">Batalkan Transaksi</button>
            </form>
        </div>

        <script>
            // Skrip sederhana untuk mengisi input ID Transaksi dari select
            document.getElementById('transaction_id_confirm_select').addEventListener('change', function() {
                document.getElementById('confirmForm').action = '/transactions/' + this.value + '/confirm-by-seller';
            });
            document.getElementById('transaction_id_pickup_select').addEventListener('change', function() {
                document.getElementById('pickupForm').action = '/transactions/' + this.value + '/pickup-by-distributor';
            });
            document.getElementById('transaction_id_deliver_select').addEventListener('change', function() {
                document.getElementById('deliverForm').action = '/transactions/' + this.value +
                    '/deliver-by-distributor';
            });
            document.getElementById('transaction_id_cancel_select').addEventListener('change', function() {
                const selectedId = this.value;
                document.getElementById('cancelForm').action = '/transactions/' + selectedId + '/cancel';
                // Juga update input text jika ada, meskipun mungkin tidak diperlukan jika select sudah mengatur action
                const textInput = document.getElementById('transaction_id_cancel_input');
                if (textInput) textInput.value = selectedId;
            });
            // Jika user mengetik langsung ke input text untuk cancel
            const cancelInputDirect = document.getElementById('transaction_id_cancel_input');
            if (cancelInputDirect) {
                cancelInputDirect.addEventListener('input', function() {
                    if (this.value) {
                        document.getElementById('cancelForm').action = '/transactions/' + this.value + '/cancel';
                        // Kosongkan pilihan select jika user mengetik langsung
                        document.getElementById('transaction_id_cancel_select').value = '';
                    }
                });
            }
        </script>
    @endif

</body>

</html>
