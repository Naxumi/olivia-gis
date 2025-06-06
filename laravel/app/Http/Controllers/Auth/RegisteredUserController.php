<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Pastikan Role di-import jika digunakan di create()
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk transaction
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; // Untuk logging error
use Clickbar\Magellan\Data\Geometries\Point; // Import Point

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::whereIn('name', ['buyer', 'seller', 'distributor'])->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([ // Simpan hasil validasi ke variabel
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address_detail' => ['nullable', 'string', 'max:1000'],
            'village' => ['nullable', 'string', 'max:255'],
            'subdistrict' => ['nullable', 'string', 'max:255'],
            'city_regency' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            //'address_notes' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $locationPoint = null;
            if (isset($validatedData['latitude']) && isset($validatedData['longitude'])) {
                // SRID 4326 adalah default untuk makeGeodetic
                $locationPoint = Point::makeGeodetic(
                    latitude: (float)$validatedData['latitude'],
                    longitude: (float)$validatedData['longitude']
                );
            }

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone_number' => $validatedData['phone_number'],
                'address_detail' => $validatedData['address_detail'],
                'village' => $validatedData['village'],
                'subdistrict' => $validatedData['subdistrict'],
                'city_regency' => $validatedData['city_regency'],
                'province' => $validatedData['province'],
                'postal_code' => $validatedData['postal_code'],
                'location' => $locationPoint, // Simpan objek Point
                //'address_notes' => $validatedData['address_notes'],
            ]);

            $user->roles()->attach($validatedData['role_id']);
            event(new Registered($user));
            Auth::login($user);
            DB::commit();

            return redirect(route('map.interactive', absolute: false));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registrasi gagal: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return back()->withInput()->withErrors(['error' => 'Registrasi gagal, silakan coba lagi.']);
        }
    }
}
