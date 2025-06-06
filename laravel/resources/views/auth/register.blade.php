<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 dark:from-gray-800 dark:to-green-900 p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-lg px-8 py-10 my-8 bg-white rounded-2xl shadow-2xl dark:bg-gray-900/80 backdrop-blur-sm">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-green-700 dark:text-green-400">Buat Akun Baru</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Bergabunglah dalam revolusi pengelolaan limbah.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- KELOMPOK 1: INFORMASI AKUN --}}
                <div class="space-y-4">
                    {{-- ... (kode untuk Nama, Email, Peran tidak berubah) ... --}}
                    <h3 class="text-lg font-semibold border-b border-green-200 dark:border-green-800 pb-2 text-gray-800 dark:text-gray-200">Informasi Akun</h3>
                    
                    <div>
                        <x-input-label for="name" value="Nama Lengkap" class="text-green-800 dark:text-green-300" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                            </div>
                            <x-text-input id="name" class="block w-full mt-1 pl-10 dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Hafizh"/>
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" class="text-green-800 dark:text-green-300" />
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                            </div>
                            <x-text-input id="email" class="block w-full mt-1 pl-10 dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="anda@contoh.com"/>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role_id" value="Mendaftar sebagai" class="text-green-800 dark:text-green-300" />
                        <select id="role_id" name="role_id" class="block w-full mt-1 border-gray-300 dark:border-green-800 dark:bg-gray-800 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Peran Anda --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                    </div>
                </div>

                {{-- KELOMPOK 2: DETAIL ALAMAT & KONTAK --}}
                <div class="mt-6 space-y-4">
                    <h3 class="text-lg font-semibold border-b border-green-200 dark:border-green-800 pb-2 text-gray-800 dark:text-gray-200">Alamat & Kontak</h3>

                     <div>
                        <x-input-label for="phone_number" value="Nomor Telepon" class="text-green-800 dark:text-green-300"/>
                        <x-text-input id="phone_number" class="block mt-1 w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="phone_number" :value="old('phone_number')" placeholder="081234567890" />
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address_detail" value="Detail Alamat (Jalan, Nomor, RT/RW)" class="text-green-800 dark:text-green-300"/>
                        <textarea id="address_detail" name="address_detail" rows="3" class="block mt-1 w-full border-gray-300 dark:border-green-800 dark:bg-gray-800 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">{{ old('address_detail') }}</textarea>
                        <x-input-error :messages="$errors->get('address_detail')" class="mt-2" />
                    </div>

                    {{-- Grid untuk alamat yang lebih rapi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="village" value="Desa/Kelurahan" class="text-green-800 dark:text-green-300"/>
                            <x-text-input id="village" class="block mt-1 w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="village" :value="old('village')" />
                        </div>
                        <div>
                            <x-input-label for="subdistrict" value="Kecamatan" class="text-green-800 dark:text-green-300"/>
                            <x-text-input id="subdistrict" class="block mt-1 w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="subdistrict" :value="old('subdistrict')" />
                        </div>
                        <div>
                            <x-input-label for="city_regency" value="Kota/Kabupaten" class="text-green-800 dark:text-green-300"/>
                            <x-text-input id="city_regency" class="block mt-1 w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="city_regency" :value="old('city_regency', 'Kota Malang')" required />
                        </div>
                        <div>
                            <x-input-label for="province" value="Provinsi" class="text-green-800 dark:text-green-300"/>
                            <x-text-input id="province" class="block mt-1 w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="province" :value="old('province', 'Jawa Timur')" required />
                        </div>
                        {{-- =============================================== --}}
                        {{--               INPUT KODE POS BARU               --}}
                        {{-- =============================================== --}}
                        <div>
                            <x-input-label for="postal_code" value="Kode Pos" class="text-green-800 dark:text-green-300"/>
                            <x-text-input id="postal_code" class="block mt-1 w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="postal_code" :value="old('postal_code')" placeholder="Contoh: 65141" />
                            <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Input Lokasi (Latitude/Longitude) --}}
                    <div id="location-container" style="display: none;">
                        {{-- ... (kode untuk input lokasi tidak berubah) ... --}}
                        <div class="flex items-center justify-between mb-2">
                            <label class="block font-medium text-sm text-green-800 dark:text-green-300">Koordinat Lokasi</label>
                            <button type="button" id="get-location-btn" class="text-sm text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.1.4-.27.615-.454L16 14.8V9.818l-1.834-1.833a4.5 4.5 0 00-6.332 0L6 9.818v4.982l4.076 3.262zM10 12a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /><path d="M10 4a6 6 0 100 12 6 6 0 000-12zM3 10a7 7 0 1114 0 7 7 0 01-14 0z" /></svg>
                                Dapatkan Lokasi Saya
                            </button>
                        </div>
                        <div id="location-status" class="text-xs text-gray-500 dark:text-gray-400 mb-2"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="latitude" value="Latitude" class="sr-only"/>
                                <x-text-input id="latitude" class="block w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="latitude" :value="old('latitude')" placeholder="Latitude" />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="longitude" value="Longitude" class="sr-only"/>
                                <x-text-input id="longitude" class="block w-full dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="text" name="longitude" :value="old('longitude')" placeholder="Longitude" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KELOMPOK 3: KATA SANDI --}}
                <div class="mt-6 space-y-4">
                   {{-- ... (kode untuk password tidak berubah) ... --}}
                   <h3 class="text-lg font-semibold border-b border-green-200 dark:border-green-800 pb-2 text-gray-800 dark:text-gray-200">Keamanan Akun</h3>

                    <div>
                        <x-input-label for="password" value="Password" class="text-green-800 dark:text-green-300"/>
                        <x-text-input id="password" class="block w-full mt-1 dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="password" name="password" required autocomplete="new-password" placeholder="••••••••"/>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" class="text-green-800 dark:text-green-300"/>
                        <x-text-input id="password_confirmation" class="block w-full mt-1 dark:bg-gray-800 dark:border-green-800 dark:focus:border-green-500 dark:focus:ring-green-500" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••"/>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>


                {{-- Tombol & Tautan --}}
                <div class="flex items-center justify-between mt-8">
                    {{-- ... (kode tombol tidak berubah) ... --}}
                     <a class="underline text-sm text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" href="{{ route('login') }}">
                        {{ __('Sudah punya akun?') }}
                    </a>

                    <x-primary-button class="ms-4 bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:ring-green-500 py-2.5 px-6">
                        {{ __('Daftar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script untuk menampilkan/menyembunyikan dan mendapatkan lokasi --}}
    <script>
        {{-- ... (script tidak berubah) ... --}}
         document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role_id');
            const locationContainer = document.getElementById('location-container');
            const getLocationBtn = document.getElementById('get-location-btn');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const locationStatus = document.getElementById('location-status');

            function toggleLocationFields() {
                const selectedRoleText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase().trim();
                
                if (selectedRoleText !== 'distributor' && roleSelect.value !== '') {
                    locationContainer.style.display = 'block';
                } else {
                    locationContainer.style.display = 'none';
                }
            }

            function getLocation() {
                if (!navigator.geolocation) {
                    locationStatus.textContent = 'Geolocation tidak didukung oleh browser Anda.';
                    locationStatus.style.color = 'red';
                    return;
                }

                locationStatus.textContent = 'Mendeteksi lokasi...';
                locationStatus.style.color = ''; // Reset color

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        latitudeInput.value = latitude;
                        longitudeInput.value = longitude;
                        locationStatus.textContent = 'Lokasi berhasil dideteksi!';
                        locationStatus.style.color = 'green';
                    },
                    () => {
                        locationStatus.textContent = 'Gagal mendapatkan lokasi. Pastikan Anda mengizinkan akses lokasi.';
                        locationStatus.style.color = 'red';
                    }
                );
            }

            // Jalankan saat halaman dimuat
            toggleLocationFields();

            // Tambahkan event listener
            roleSelect.addEventListener('change', toggleLocationFields);
            getLocationBtn.addEventListener('click', getLocation);
        });
    </script>
</x-guest-layout>