<?php

namespace App\Http\Controllers;

use App\Models\Land;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lands = Auth::user()->lands()->with(['sensorData' => function($query) {
            $query->latest()->take(1);
        }])->get();

        return view('Monitoring.monitorings', compact('lands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Monitoring.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Masuk ke method store LandController');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'area' => 'required|numeric|min:0.1',
            'device_id' => 'required|string|max:255'
        ]);

        // Validasi kustom untuk device_id
        $orderItem = \App\Models\OrderItem::where('device_id', $validated['device_id'])
                                            ->whereHas('order', function($query) {
                                                $query->where('user_id', Auth::id());
                                            })
                                            ->first();

        if (!$orderItem) {
            return redirect()->back()->withErrors(['device_id' => 'Device ID tidak valid atau tidak terdaftar di akun Anda.'])->withInput();
        }
        
        // Cek apakah device_id sudah digunakan oleh lahan lain
        $existingLand = Land::where('device_id', $validated['device_id'])->first();
        if ($existingLand) {
            return redirect()->back()->withErrors(['device_id' => 'Device ID ini sudah digunakan untuk lahan lain.'])->withInput();
        }

        $land = Auth::user()->lands()->create($validated);

        // Tambahkan log sebelum insert
        Log::info('Akan insert sensor data', ['land_id' => $land->id]);

        try {
            SensorData::create([
                'land_id' => $land->id,
                'ph_value' => rand(55, 75) / 10, // 5.5 - 7.5
                'moisture_value' => rand(600, 900) / 10, // 60 - 90
                'temperature' => rand(250, 350) / 10, // 25 - 35
                'humidity' => rand(500, 900) / 10, // 50 - 90
                'timestamp' => now(),
            ]);
            Log::info('Insert sensor data berhasil', ['land_id' => $land->id]);
        } catch (\Exception $e) {
            Log::error('Insert sensor data gagal', ['error' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lahan berhasil ditambahkan',
                'data' => $land
            ]);
        }

        return redirect()->route('monitoring')->with('success', 'Lahan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Land $land)
    {
        $this->authorize('view', $land);

        $land->load(['sensorData' => function($query) {
            $query->latest()->take(1);
        }]);

        return view('Monitoring.land-detail', compact('land'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Land $land)
    {
        $this->authorize('update', $land);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'area' => 'required|numeric|min:0.1',
            'device_id' => 'required|string|max:255|unique:lands,device_id,' . $land->id
        ]);

        $land->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lahan berhasil diperbarui',
            'data' => $land
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Land $land)
    {
        $this->authorize('delete', $land);

        $land->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lahan berhasil dihapus'
        ]);
    }

    public function getLatestData(Land $land)
    {
        $this->authorize('view', $land);

        // Generate data baru setiap kali dipanggil
        $data = [
            'ph_value' => rand(55, 75) / 10, // 5.5 - 7.5
            'moisture_value' => rand(600, 900) / 10, // 60 - 90
            'temperature' => rand(250, 350) / 10, // 25 - 35
            'humidity' => rand(500, 900) / 10, // 50 - 90
            'timestamp' => now(),
        ];

        // Simpan data baru ke database
        $sensorData = SensorData::create([
            'land_id' => $land->id,
            'ph_value' => $data['ph_value'],
            'moisture_value' => $data['moisture_value'],
            'temperature' => $data['temperature'],
            'humidity' => $data['humidity'],
            'timestamp' => $data['timestamp'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getSensorData(Land $land)
    {
        $this->authorize('view', $land);

        $sensorData = $land->sensorData()
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $sensorData
        ]);
    }
}
