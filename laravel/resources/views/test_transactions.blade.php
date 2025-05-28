<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Halaman Transaksi</title>
    <style>
        /* General Reset & Base Styles */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            margin: 0;
            background-color: #f3f4f6; /* Light gray background */
            color: #1f2937; /* Dark gray text */
            line-height: 1.6;
            padding: 20px;
        }

        /* Page Title */
        h1 {
            font-size: 1.875rem; /* text-2xl like */
            font-weight: 600; /* font-semibold */
            color: #111827; /* Very dark gray / black */
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Content Cards */
        .container {
            background-color: #ffffff; /* White background */
            padding: 1.5rem; /* p-6 like */
            border-radius: 0.5rem; /* rounded-lg */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); /* shadow-md like */
            margin-bottom: 1.5rem; /* mb-6 */
        }

        /* Headings within Cards */
        .container h2 {
            font-size: 1.25rem; /* text-xl like */
            font-weight: 600; /* font-semibold */
            color: #1f2937; /* Dark gray */
            margin-top: 0;
            margin-bottom: 1rem; /* mb-4 */
            padding-bottom: 0.75rem; /* pb-3 */
            border-bottom: 1px solid #e5e7eb; /* border-gray-200 like */
        }

        /* Descriptive paragraphs under H2 in cards */
        .container > p {
            font-size: 0.875rem; /* text-sm like */
            color: #4b5563; /* Gray text */
            margin-top: -0.5rem; /* Pull up slightly if h2 has margin-bottom */
            margin-bottom: 1rem; /* mb-4 */
        }

        /* Form Styling */
        form {
            margin-bottom: 0; /* Remove original spacing, manage with form-section */
            padding-bottom: 0;
            border-bottom: none;
        }

        .form-section { /* Helper class for spacing between label-input groups */
            margin-bottom: 1.25rem; /* mb-5 like */
        }
        .form-section:last-of-type { /* Spacing before submit button */
            margin-bottom: 1.5rem; /* mb-6 like */
        }

        label {
            display: block;
            margin-bottom: 0.5rem; /* mb-2 */
            font-weight: 500; /* font-medium */
            font-size: 0.875rem; /* text-sm */
            color: #374151; /* Slightly lighter dark gray */
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.625rem 0.75rem; /* py-2.5 px-3 like */
            border: 1px solid #d1d5db; /* Light border */
            border-radius: 0.375rem; /* rounded-md */
            box-sizing: border-box;
            font-size: 0.875rem; /* text-sm */
            background-color: #fff;
            color: #1f2937;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            border-color: #2563eb; /* Blue focus border */
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4); /* Blue focus ring */
        }
        textarea {
            min-height: 80px;
            resize: vertical;
        }

        /* Button Styling */
        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #2563eb; /* Blue button */
            color: white;
            padding: 0.625rem 1.25rem; /* py-2.5 px-5 like */
            border: 1px solid transparent;
            border-radius: 0.375rem; /* rounded-md */
            cursor: pointer;
            font-size: 0.875rem; /* text-sm */
            font-weight: 500; /* font-medium */
            transition: background-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            text-decoration: none;
        }
        button:hover {
            background-color: #1d4ed8; /* Darker blue on hover */
        }
        button:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5); /* Focus ring */
        }

        /* Danger Button (e.g., Logout) */
        button.button-danger, button[style*="background-color:#dc3545"] {
            background-color: #dc2626 !important; /* Red button, !important to override inline style if needed */
        }
        button.button-danger:hover, button[style*="background-color:#dc3545"]:hover {
            background-color: #b91c1c !important; /* Darker red on hover */
        }
        button.button-danger:focus, button[style*="background-color:#dc3545"]:focus {
             box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.5); /* Red focus ring */
        }

        /* User Info Box Styling */
        .user-info {
            background-color: #e0e7ff; /* Light indigo background */
            color: #3730a3; /* Indigo text */
            padding: 1rem; /* p-4 like */
            border-radius: 0.5rem; /* rounded-lg */
            margin-bottom: 1.5rem; /* mb-6 */
            border: 1px solid #c7d2fe; /* Indigo border */
        }
        .user-info p {
            margin-top: 0;
            margin-bottom: 0.5rem; /* mb-2 */
        }
        .user-info strong {
            color: #312e81; /* Darker indigo */
            font-weight: 600; /* font-semibold */
        }
        .user-info .role-badge, .user-info span[style*="background-color: #007bff"] { /* Target new and old role badges */
            background-color: #4f46e5 !important; /* Indigo badge, !important to override inline style */
            color: white !important;
            padding: 0.125rem 0.5rem !important; /* py-0.5 px-2 like */
            border-radius: 0.25rem !important; /* rounded-sm */
            font-size: 0.75rem !important; /* text-xs */
            font-weight: 500 !important; /* font-medium */
            margin-right: 0.25rem; /* mr-1 */
            display: inline-block;
        }
        .user-info form { /* For the logout form inside user-info */
            margin-top: 0.75rem; /* mt-3 */
        }

        /* Login Link (if not logged in) */
        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
            color: #1d4ed8;
        }

        /* Dark Mode (OS preference based) */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827; /* Dark background */
                color: #d1d5db; /* Light gray text */
            }
            h1 {
                color: #f9fafb; /* Off-white */
            }
            .container {
                background-color: #1f2937; /* Darker card background */
                border-color: #374151; /* Darker border for cards if any */
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -2px rgba(0, 0, 0, 0.4);
            }
            .container h2 {
                color: #f3f4f6; /* Lighter text for h2 */
                border-bottom-color: #4b5563; /* Darker border */
            }
            .container > p {
                color: #9ca3af; /* Lighter gray text */
            }
            label {
                color: #9ca3af; /* Lighter gray for labels */
            }
            input[type="text"],
            input[type="number"],
            select,
            textarea {
                background-color: #374151; /* Dark input background */
                border-color: #4b5563; /* Darker input border */
                color: #f3f4f6; /* Light text in inputs */
            }
            input[type="text"]:focus,
            input[type="number"]:focus,
            select:focus,
            textarea:focus {
                border-color: #3b82f6; /* Lighter blue focus border */
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3); /* Lighter blue focus ring */
            }

            .user-info {
                background-color: #3730a3; /* Darker indigo background */
                color: #e0e7ff; /* Lighter indigo text */
                border-color: #4338ca; /* Even darker indigo border */
            }
            .user-info strong {
                color: #c7d2fe; /* Very light indigo */
            }
            .user-info .role-badge, .user-info span[style*="background-color: #007bff"] {
                background-color: #a5b4fc !important; /* Lighter indigo badge */
                color: #1e1b4b !important; /* Dark text on light badge */
            }

            .login-link a {
                color: #60a5fa; /* Lighter blue link */
            }
            .login-link a:hover {
                color: #93c5fd;
            }
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
                        {{-- Inline style akan di-override oleh CSS di atas karena !important, atau hapus inline style --}}
                        <span class="role-badge">{{ $role->name }}</span>
                    @endforeach
                @else
                    <span>Tidak ada peran</span>
                @endif
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                {{-- Inline style akan di-override, atau hapus dan tambahkan class button-danger --}}
                <button type="submit" class="button-danger">Logout</button>
            </form>
        </div>
    @else
        <div class="container login-link"> {{-- Added container for consistent look and login-link class --}}
            <p><a href="{{ route('login') }}">Login dulu</a> untuk menguji.</p>
        </div>
    @endif

    @if (Auth::check())
        <div class="container">
            <h2>1. Buat Transaksi Baru (sebagai Buyer)</h2>
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="form-section">
                    <label for="waste_variant_id_store">ID Varian Limbah:</label>
                    <select name="waste_variant_id" id="waste_variant_id_store" required>
                        <option value="">Pilih Varian Limbah</option>
                        @foreach ($waste_variants as $variant)
                            <option value="{{ $variant->id }}">ID: {{ $variant->id }} (Stok: {{ $variant->stock }}, Harga:
                                {{ $variant->price }}) - Milik Waste ID: {{ $variant->waste_id }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-section">
                    <label for="quantity_store">Jumlah:</label>
                    <input type="number" id="quantity_store" name="quantity" value="1" min="1" required>
                </div>

                <div class="form-section">
                    <label for="payment_method_store">Metode Pembayaran:</label>
                    <select name="payment_method" id="payment_method_store" required>
                        <option value="cod">COD (Cash on Delivery)</option>
                        <option value="bank_transfer_bca">Transfer Bank BCA</option>
                        <option value="dll">DLL (Metode Lain)</option>
                    </select>
                </div>
                <button type="submit">Buat Transaksi</button>
            </form>
        </div>

        <div class="container">
            <h2>2. Konfirmasi Transaksi (sebagai Seller/Admin)</h2>
            <p>Pilih ID transaksi yang masih 'pending'.</p>
            <form action="" method="POST" id="confirmForm">
                @csrf
                @method('PATCH')
                <div class="form-section">
                    <label for="transaction_id_confirm_select">ID Transaksi (Pending):</label>
                    <select name="transaction_id_confirm_select" id="transaction_id_confirm_select"
                        onchange="document.getElementById('confirmForm').action = '/transactions/' + this.value + '/confirm-by-seller';">
                        <option value="">Pilih Transaksi Pending</option>
                        @foreach ($transactions_pending as $transaction)
                            <option value="{{ $transaction->id }}">ID: {{ $transaction->id }} (Buyer:
                                {{ $transaction->buyer_id }}, Seller: {{ $transaction->seller_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-section">
                    <label for="distributor_id_confirm">(Opsional) Assign ID Distributor:</label>
                    <select name="distributor_id" id="distributor_id_confirm">
                        <option value="">Tidak Assign Distributor</option>
                        @foreach ($users as $user)
                            @if ($user->hasRole('distributor'))
                                <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <button type="submit">Konfirmasi Transaksi</button>
            </form>
        </div>

        <div class="container">
            <h2>3. Tandai Sudah Diambil (sebagai Distributor)</h2>
            <p>Pilih ID transaksi yang statusnya 'confirmed'.</p>
            <form action="" method="POST" id="pickupForm">
                @csrf
                @method('PATCH')
                <div class="form-section">
                    <label for="transaction_id_pickup_select">ID Transaksi (Confirmed):</label>
                    <select name="transaction_id_pickup_select" id="transaction_id_pickup_select"
                        onchange="document.getElementById('pickupForm').action = '/transactions/' + this.value + '/pickup-by-distributor';">
                        <option value="">Pilih Transaksi Confirmed</option>
                        @foreach ($transactions_confirmed as $transaction)
                            <option value="{{ $transaction->id }}">ID: {{ $transaction->id }} (Logistics ID:
                                {{ $transaction->logistics_id ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit">Tandai Sudah Diambil</button>
            </form>
        </div>

        <div class="container">
            <h2>4. Tandai Sudah Terkirim (sebagai Distributor)</h2>
            <p>Pilih ID transaksi yang statusnya 'picked_up'.</p>
            <form action="" method="POST" id="deliverForm">
                @csrf
                @method('PATCH')
                 <div class="form-section">
                    <label for="transaction_id_deliver_select">ID Transaksi (Picked Up):</label>
                    <select name="transaction_id_deliver_select" id="transaction_id_deliver_select"
                        onchange="document.getElementById('deliverForm').action = '/transactions/' + this.value + '/deliver-by-distributor';">
                        <option value="">Pilih Transaksi Picked Up</option>
                        @foreach ($transactions_picked_up as $transaction)
                            <option value="{{ $transaction->id }}">ID: {{ $transaction->id }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit">Tandai Sudah Terkirim</button>
            </form>
        </div>

        <div class="container">
            <h2>5. Batalkan Transaksi</h2>
            <p>Pilih ID transaksi yang ingin dibatalkan (misalnya yang 'pending' atau 'confirmed').</p>
            <form action="" method="POST" id="cancelForm">
                @csrf
                @method('PATCH')
                <div class="form-section">
                    <label for="transaction_id_cancel_input">ID Transaksi (Ketik atau Pilih):</label>
                    <input type="number" id="transaction_id_cancel_input" name="transaction_id_cancel_input_direct"
                        placeholder="Masukkan ID Transaksi">
                </div>
                <div class="form-section">
                    <label for="transaction_id_cancel_select">Atau Pilih dari Daftar:</label>
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
                </div>
                <div class="form-section">
                    <label for="reason_cancel">Alasan Pembatalan (opsional):</label>
                    <textarea id="reason_cancel" name="reason"></textarea>
                </div>
                <button type="submit" class="button-danger">Batalkan Transaksi</button>
            </form>
        </div>

        <script>
            // Skrip sederhana untuk mengisi input ID Transaksi dari select
            // Fungsi pembantu untuk mengatur action form berdasarkan pilihan select
            function setupFormActionUpdater(selectId, formId, urlPrefix, urlSuffix, alsoUpdateInputId = null) {
                const selectElement = document.getElementById(selectId);
                const formElement = document.getElementById(formId);
                if (selectElement && formElement) {
                    selectElement.addEventListener('change', function() {
                        if (this.value) { // Hanya set action jika ada value yang dipilih
                            formElement.action = `${urlPrefix}${this.value}${urlSuffix}`;
                            if (alsoUpdateInputId) {
                                const textInput = document.getElementById(alsoUpdateInputId);
                                if (textInput) textInput.value = this.value;
                            }
                        } else {
                            formElement.action = ''; // Kosongkan action jika tidak ada yang dipilih
                        }
                    });
                }
            }

            setupFormActionUpdater('transaction_id_confirm_select', 'confirmForm', '/transactions/', '/confirm-by-seller');
            setupFormActionUpdater('transaction_id_pickup_select', 'pickupForm', '/transactions/', '/pickup-by-distributor');
            setupFormActionUpdater('transaction_id_deliver_select', 'deliverForm', '/transactions/', '/deliver-by-distributor');
            setupFormActionUpdater('transaction_id_cancel_select', 'cancelForm', '/transactions/', '/cancel', 'transaction_id_cancel_input');

            const cancelInputDirect = document.getElementById('transaction_id_cancel_input');
            const cancelSelect = document.getElementById('transaction_id_cancel_select'); // Ambil elemen select
            const cancelForm = document.getElementById('cancelForm'); // Ambil elemen form

            if (cancelInputDirect && cancelForm) {
                cancelInputDirect.addEventListener('input', function() {
                    if (this.value) {
                        cancelForm.action = '/transactions/' + this.value + '/cancel';
                        if (cancelSelect) cancelSelect.value = ''; // Kosongkan pilihan select jika user mengetik langsung
                    } else if (cancelSelect && cancelSelect.value === '') { // jika input kosong DAN select juga kosong
                        cancelForm.action = ''; // Kosongkan action jika input ID kosong dan select juga tidak dipilih
                    }
                });
            }
        </script>
    @endif

</body>
</html>