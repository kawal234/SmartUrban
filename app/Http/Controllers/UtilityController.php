<?php

namespace App\Http\Controllers;

use App\Models\UtilityUsage;
use App\Models\TrafficZone;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function index()
    {
        $utilities = UtilityUsage::with('trafficZone')
            ->orderBy('reading_time', 'desc')
            ->paginate(15);

        return view('utilities.index', compact('utilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'traffic_zone_id' => 'required|exists:traffic_zones,id',
            'type' => 'required|in:electricity,water',
            'consumption_rate' => 'required|numeric|min:0',
            'threshold' => 'required|numeric|min:0',
            'reading_time' => 'required|date'
        ]);

        UtilityUsage::create($validated);

        return redirect()->route('utilities.index')
            ->with('success', 'Utility usage recorded successfully');
    }

    public function getZoneStats($zoneId)
    {
        $zone = TrafficZone::findOrFail($zoneId);
        
        $stats = UtilityUsage::where('traffic_zone_id', $zoneId)
            ->selectRaw('type, AVG(consumption_rate) as avg_consumption, MAX(consumption_rate) as peak_consumption')
            ->groupBy('type')
            ->get();

        return response()->json([
            'zone' => $zone,
            'stats' => $stats
        ]);
    }

    public function getAlerts()
    {
        $alerts = UtilityUsage::with('trafficZone')
            ->whereRaw('consumption_rate > threshold')
            ->orderBy('reading_time', 'desc')
            ->get();

        return response()->json($alerts);
    }

    public function getDailyUsage(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:electricity,water',
            'date' => 'required|date'
        ]);

        $usage = UtilityUsage::where('type', $validated['type'])
            ->whereDate('reading_time', $validated['date'])
            ->selectRaw('HOUR(reading_time) as hour, AVG(consumption_rate) as avg_consumption')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json($usage);
    }

    public function exportReport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:electricity,water'
        ]);

        $data = UtilityUsage::with('trafficZone')
            ->where('type', $validated['type'])
            ->whereBetween('reading_time', [$validated['start_date'], $validated['end_date']])
            ->get();

        // Here you would typically use a package like maatwebsite/excel to generate the report
        // For now, we'll just return JSON
        return response()->json($data);
    }
} 