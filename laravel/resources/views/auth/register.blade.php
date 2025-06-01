<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role -->
        <div>
            <x-input-label for="role_id" :value="__('Register as')" />
            <select id="role_id" name="role_id" class="block mt-1 w-full" required>
                <option value="">-- Select Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>

        {{-- Tambahkan field alamat mulai dari sini --}}
        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Nomor Telepon')" />
            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number"
                :value="old('phone_number')" />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="address_detail" :value="__('Detail Alamat (Jalan, Nomor, RT/RW)')" />
            <textarea id="address_detail" name="address_detail"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('address_detail') }}</textarea>
            <x-input-error :messages="$errors->get('address_detail')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="village" :value="__('Desa/Kelurahan')" />
            <x-text-input id="village" class="block mt-1 w-full" type="text" name="village" :value="old('village')" />
            <x-input-error :messages="$errors->get('village')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="subdistrict" :value="__('Kecamatan')" />
            <x-text-input id="subdistrict" class="block mt-1 w-full" type="text" name="subdistrict"
                :value="old('subdistrict')" />
            <x-input-error :messages="$errors->get('subdistrict')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="city_regency" :value="__('Kota/Kabupaten')" />
            <x-text-input id="city_regency" class="block mt-1 w-full" type="text" name="city_regency"
                :value="old('city_regency')" required />
            <x-input-error :messages="$errors->get('city_regency')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="province" :value="__('Provinsi')" />
            <x-text-input id="province" class="block mt-1 w-full" type="text" name="province" :value="old('province')"
                required />
            <x-input-error :messages="$errors->get('province')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="postal_code" :value="__('Kode Pos')" />
            <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code"
                :value="old('postal_code')" />
            <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="latitude" :value="__('Latitude (Opsional)')" />
            <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" :value="old('latitude')"
                placeholder="Contoh: -7.983908" />
            <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="longitude" :value="__('Longitude (Opsional)')" />
            <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" :value="old('longitude')"
                placeholder="Contoh: 112.621391" />
            <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="address_notes" :value="__('Catatan Alamat (Opsional)')" />
            <textarea id="address_notes" name="address_notes"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('address_notes') }}</textarea>
            <x-input-error :messages="$errors->get('address_notes')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
