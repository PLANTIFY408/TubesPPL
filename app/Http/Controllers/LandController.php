<?php

namespace App\Http\Controllers;

use App\Models\Land;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'area' => 'required|numeric|min:0.1',
            'device_id' => 'required|string|max:255|unique:lands'
        ]);

        $land = Auth::user()->lands()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lahan berhasil ditambahkan',
            'data' => $land
        ]);
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

        $latestData = $land->sensorData()->latest()->first();

        if (!$latestData) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada data sensor'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $latestData
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
