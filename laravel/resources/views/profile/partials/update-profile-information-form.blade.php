<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- AWAL PENAMBAHAN INPUT FIELD BARU --}}

        <div>
            <x-input-label for="phone_number" :value="__('Nomor Telepon')" />
            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full"
                :value="old('phone_number', $user->phone_number)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        <div>
            <x-input-label for="address_detail" :value="__('Detail Alamat')" />
            {{-- Untuk field teks panjang seperti alamat, pertimbangkan menggunakan x-textarea jika tersedia, atau styling x-text-input --}}
            <x-text-input id="address_detail" name="address_detail" type="text" class="mt-1 block w-full"
                :value="old('address_detail', $user->address_detail)" autocomplete="street-address" />
            <x-input-error class="mt-2" :messages="$errors->get('address_detail')" />
        </div>

        <div>
            <x-input-label for="village" :value="__('Desa/Kelurahan')" />
            <x-text-input id="village" name="village" type="text" class="mt-1 block w-full" :value="old('village', $user->village)" />
            <x-input-error class="mt-2" :messages="$errors->get('village')" />
        </div>

        <div>
            <x-input-label for="subdistrict" :value="__('Kecamatan')" />
            <x-text-input id="subdistrict" name="subdistrict" type="text" class="mt-1 block w-full"
                :value="old('subdistrict', $user->subdistrict)" />
            <x-input-error class="mt-2" :messages="$errors->get('subdistrict')" />
        </div>

        <div>
            <x-input-label for="city_regency" :value="__('Kota/Kabupaten')" />
            <x-text-input id="city_regency" name="city_regency" type="text" class="mt-1 block w-full"
                :value="old('city_regency', $user->city_regency)" autocomplete="address-level2" />
            <x-input-error class="mt-2" :messages="$errors->get('city_regency')" />
        </div>

        <div>
            <x-input-label for="province" :value="__('Provinsi')" />
            <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $user->province)"
                autocomplete="address-level1" />
            <x-input-error class="mt-2" :messages="$errors->get('province')" />
        </div>

        <div>
            <x-input-label for="postal_code" :value="__('Kode Pos')" />
            <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full"
                :value="old('postal_code', $user->postal_code)" autocomplete="postal-code" />
            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
        </div>

        <div>
            <x-input-label for="latitude" :value="__('Latitude')" />
            <x-text-input id="latitude" name="latitude" type="number" step="any" class="mt-1 block w-full"
                :value="old('latitude', $user->location ? $user->location->getLatitude() : '')" />
            <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
        </div>

        <div>
            <x-input-label for="longitude" :value="__('Longitude')" />
            <x-text-input id="longitude" name="longitude" type="number" step="any" class="mt-1 block w-full"
                :value="old('longitude', $user->location ? $user->location->getLongitude() : '')" />
            <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
        </div>

        <div>
            <x-input-label for="address_notes" :value="__('Catatan Alamat')" />
            {{-- Pertimbangkan menggunakan x-textarea jika tersedia --}}
            <x-text-input id="address_notes" name="address_notes" type="text" class="mt-1 block w-full"
                :value="old('address_notes', $user->address_notes)" />
            <x-input-error class="mt-2" :messages="$errors->get('address_notes')" />
        </div>

        {{-- AKHIR PENAMBAHAN INPUT FIELD BARU --}}

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
