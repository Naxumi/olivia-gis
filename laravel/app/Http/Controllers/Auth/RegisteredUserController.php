<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Role;
use Illuminate\Support\Facades\DB; // Untuk transaction
use Illuminate\Validation\Rules\Enum; // Jika role diambil dari Enum


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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'], // Pastikan role_id yang dipilih valid
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->roles()->attach($request->role_id); // Menghubungkan user dengan role

            event(new Registered($user));
            Auth::login($user);
            DB::commit();
            return redirect(route('dashboard', absolute: false)); // Atau halaman lain

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error atau tampilkan pesan error
            return back()->withInput()->withErrors(['error' => 'Registrasi gagal, silakan coba lagi.']);
        }
    }
}
