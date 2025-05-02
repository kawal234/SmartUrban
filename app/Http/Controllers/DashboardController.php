<?php

namespace App\Http\Controllers;

use App\Models\TrafficZone;
use App\Models\TransportRoute;
use App\Models\UtilityUsage;
use App\Models\LogisticsRoute;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalRoutes = TransportRoute::count();
        $activeRoutes = TransportRoute::where('status', 'active')->count();
        $delayedRoutes = TransportRoute::where('status', 'delayed')->count();
        $totalUtilityUsage = UtilityUsage::count();

        // Get recent utility usage
        $utilityUsage = UtilityUsage::latest()->take(10)->get();

        // Get traffic zones for the map
        $trafficZones = TrafficZone::select('name', 'latitude', 'longitude', 'congestion_level', 'status')
            ->get()
            ->map(function ($zone) {
                return [
                    'name' => $zone->name,
                    'latitude' => $zone->latitude,
                    'longitude' => $zone->longitude,
                    'congestion_level' => $zone->congestion_level,
                    'status' => $zone->status
                ];
            });

        return view('dashboard', compact(
            'totalRoutes',
            'activeRoutes',
            'delayedRoutes',
            'totalUtilityUsage',
            'utilityUsage',
            'trafficZones'
        ));
    }

    public function getMapData()
    {
        $zones = TrafficZone::all();
        $routes = TransportRoute::all();
        $logistics = LogisticsRoute::where('status', 'in_progress')->get();

        return response()->json([
            'zones' => $zones,
            'routes' => $routes,
            'logistics' => $logistics
        ]);
    }

    public function getAnalytics()
    {
        // Calculate system-wide statistics
        $stats = [
            'average_congestion' => TrafficZone::avg('congestion_level'),
            'total_active_routes' => LogisticsRoute::where('status', '!=', 'completed')->count(),
            'delayed_deliveries' => LogisticsRoute::where('delay_minutes', '>', 0)->count(),
            'utility_alerts' => UtilityUsage::where('consumption_rate', '>', 'threshold')->count()
        ];

        return response()->json($stats);
    }
} 