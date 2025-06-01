<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point dari Magellan

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        // Isi atribut pengguna dengan data yang divalidasi (kecuali lat/lon)
        $user->fill($validatedData);

        // Handle pembuatan Point jika latitude dan longitude ada
        if (isset($validatedData['latitude']) && isset($validatedData['longitude'])) {
            $user->location = Point::makeGeodetic( // SRID 4326 default
                latitude: (float)$validatedData['latitude'],
                longitude: (float)$validatedData['longitude']
            );
        } elseif (array_key_exists('latitude', $validatedData) || array_key_exists('longitude', $validatedData)) {
            // Jika salah satu ada tapi tidak keduanya, atau jika dikirim sebagai null, set lokasi ke null
            // Ini berguna jika Anda ingin mengizinkan pengguna menghapus lokasi mereka
            $user->location = null;
        }
        // Jika latitude dan longitude tidak ada dalam request, $user->location tidak akan diubah
        // kecuali jika field tersebut secara eksplisit dikirim sebagai null dan Anda ingin menanganinya.

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
