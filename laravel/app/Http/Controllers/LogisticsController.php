<?php

namespace App\Http\Controllers;

use App\Models\Logistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Database\PostgisFunctions\ST; // Magellan ST functions

class LogisticsController extends Controller
{
    /**
     * Menampilkan form untuk mengupdate lokasi logistik.
     * Dipanggil oleh rute di web.php.
     */
    public function showUpdateLocationForm(Request $request, Logistics $logistics)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Otorisasi: Pastikan user adalah distributor yang memiliki logistik ini
        if (!$user->hasRole('distributor') || $logistics->distributor_id !== $user->id) {
            abort(403, 'Akses ditolak untuk halaman ini.');
        }

        // Cek apakah logistik sedang dalam perjalanan
        if ($logistics->status !== Logistics::STATUS_IN_TRANSIT) {
            return redirect()->route('dashboard') // Ganti 'dashboard' dengan rute yang sesuai
                ->with('error_message', 'Lokasi logistik #' . $logistics->id . ' tidak dapat diupdate karena pengiriman tidak sedang berlangsung.');
        }

        // Ganti 'logistics.update_location_form' dengan path view Blade Anda yang sebenarnya
        return view('logistics.update_location_form', ['logisticsItem' => $logistics]);
    }

    /**
     * Endpoint untuk distributor mengupdate lokasi mereka.
     * Bisa dipanggil dari API client (JSON) atau form Blade (redirect).
     */
    /**
     * Endpoint untuk distributor mengupdate lokasi mereka.
     * PATCH /api/logistics/{logistics}/location
     */
    public function updateLocation(Request $request, Logistics $logistics)
    {
        $distributor = Auth::user();
        if (!$distributor->hasRole('distributor') || $logistics->distributor_id !== $distributor->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($logistics->status !== Logistics::STATUS_IN_TRANSIT) {
            return response()->json(['message' => 'Pengiriman tidak sedang berlangsung.'], 422);
        }

        $validated = $request->validate([
            'current_latitude' => 'required|numeric|between:-90,90',
            'current_longitude' => 'required|numeric|between:-180,180',
        ]);

        $currentLocationPoint = Point::makeGeodetic(
            latitude: (float)$validated['current_latitude'],
            longitude: (float)$validated['current_longitude']
        );


        $logistics->current_location = $currentLocationPoint;
        $logistics->last_updated_at = Carbon::now();

        $destinationPoint = $logistics->getDestinationCoordinates(); // Ini Point atau null
        \Illuminate\Support\Facades\Log::info('Destination Point:', [$destinationPoint]); // Log ke storage/logs/laravel.log

        if ($destinationPoint instanceof Point) {
            // Menggunakan ST::distance dengan casting ke geography untuk akurasi meter
            // $currentLocationForDb = ST::makePoint($currentLocationPoint->getLongitude(), $currentLocationPoint->getLatitude())->setSRID(4326);
            // $destinationForDb = ST::makePoint($destinationPoint->getLongitude(), $destinationPoint->getLatitude())->setSRID(4326);

            // Jika kolom sudah geography, casting tidak perlu, Magellan menangani Point object
            $distanceMeters = DB::scalar(
                "SELECT ST_Distance(?, ?)",
                [$logistics->current_location, $destinationPoint]
            );
            \Illuminate\Support\Facades\Log::info('Calculated Distance (meters):', [$distanceMeters]);
            // Untuk clickbar/magellan v2, parameter ke ST::distance harus berupa ekspresi atau nama kolom.
            // Jika $currentLocationPoint dan $destinationPoint adalah objek Point dari Magellan:
            // $distanceExpression = ST::distance(new \Clickbar\Magellan\Database\Expressions\AsGeography($logistics->current_location), new \Clickbar\Magellan\Database\Expressions\AsGeography($destinationPoint));
            // $distanceMeters = Logistics::query()->select($distanceExpression->as('dist_meters'))->value('dist_meters');
            // Atau jika ingin langsung nilai dari dua objek Point:
            // $distanceMeters = $currentLocationPoint->distance($destinationPoint); // Cek apakah method distance() ada di Point Magellan dan support geography

            // Untuk kepastian, menggunakan DB::raw atau select scalar dengan cast ke geography
            // $distanceMeters = DB::selectOne(
            //     "SELECT ST_Distance(?::geography, ?::geography) as distance_meters",
            //     [$currentLocationPoint->toWKT(), $destinationPoint->toWKT()] // Kirim sebagai WKT
            // )->distance_meters;
            // Atau jika model sudah di-cast ke Point dan kolomnya geography, bisa langsung:
            // $distanceMeters = DB::table(DB::raw('DUAL')) // atau select dari tabel dummy
            //     ->select(ST::distance(
            //         $logistics->current_location, // Ini sudah objek Point dari cast
            //         $destinationPoint            // Ini juga objek Point
            //     )->as('dist_meters'))->value('dist_meters');


            if ($distanceMeters !== null) {
                $logistics->distance_km = round($distanceMeters / 1000, 2);
                $averageSpeedKmh = 30;
                if ($logistics->distance_km > 0 && $averageSpeedKmh > 0) {
                    $logistics->duration_minutes = round(($logistics->distance_km / $averageSpeedKmh) * 60);
                    $logistics->estimated_delivery_time = Carbon::now()->addMinutes($logistics->duration_minutes);
                } else {
                    $logistics->duration_minutes = 0;
                    $logistics->estimated_delivery_time = $logistics->last_updated_at;
                }
            }
        }

        $logistics->save();
        return response()->json([
            'message' => 'Lokasi dan estimasi berhasil diperbarui.',
            'logistics' => $logistics,
            'current' => $currentLocationPoint,
            'current' => $currentLocationPoint,
            'destination' => $destinationPoint,
        ]);
    }


    /**
     * Endpoint untuk klien (buyer/seller) mengambil status dan lokasi logistik.
     * GET /api/logistics/{logistics}/status
     */
    public function getLogisticsStatus(Logistics $logistics)
    {
        $user = Auth::user();
        $transaction = $logistics->transaction; // Ambil transaksi terkait

        // Otorisasi: Hanya buyer, seller dari transaksi terkait, atau admin yang bisa lihat
        if (!($user->id === $transaction->buyer_id ||
            $user->id === $transaction->seller_id ||
            $user->hasRole('admin') ||
            ($user->hasRole('distributor') && $user->id === $logistics->distributor_id) // Distributor yang bersangkutan juga boleh lihat
        )) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json(
            $logistics->load([
                'distributorUser:id,name', // Hanya ID dan nama distributor
                'distributorUser.distributorProfile:user_id,contact_person,phone_number' // Profil distributor jika ada
            ])
        );
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Logistics $logistics)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logistics $logistics)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Logistics $logistics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logistics $logistics)
    {
        //
    }
}
