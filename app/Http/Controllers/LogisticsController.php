<?php

namespace App\Http\Controllers;

use App\Models\LogisticsRoute;
use App\Models\TrafficZone;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    public function index(Request $request)
    {
        $query = LogisticsRoute::query();

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Apply delay filter
        if ($request->has('delay') && $request->delay !== '') {
            if ($request->delay === 'on_time') {
                $query->where('delay_minutes', '<=', 0);
            } else {
                $query->where('delay_minutes', '>', 0);
            }
        }

        // Apply date filter
        if ($request->has('date') && $request->date !== '') {
            $query->whereDate('created_at', $request->date);
        }

        $routes = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('logistics.index', compact('routes'));
    }

    public function create()
    {
        $zones = TrafficZone::all();
        return view('logistics.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stops' => 'required|array',
            'estimated_time_minutes' => 'required|integer|min:1',
            'traffic_zones_crossed' => 'required|array'
        ]);

        $route = LogisticsRoute::create($validated);

        return redirect()->route('logistics.show', $route)
            ->with('success', 'Route created successfully');
    }

    public function show(LogisticsRoute $route)
    {
        $affectedZones = $route->getAffectedZones();
        return view('logistics.show', compact('route', 'affectedZones'));
    }

    public function update(Request $request, LogisticsRoute $route)
    {
        $validated = $request->validate([
            'actual_time_minutes' => 'required|integer|min:1',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $validated['delay_minutes'] = max(0, $validated['actual_time_minutes'] - $route->estimated_time_minutes);
        
        if ($validated['status'] === 'in_progress' && !$route->start_time) {
            $validated['start_time'] = now();
        } elseif ($validated['status'] === 'completed' && !$route->end_time) {
            $validated['end_time'] = now();
        }

        $route->update($validated);

        return redirect()->route('logistics.show', $route)
            ->with('success', 'Route updated successfully');
    }

    public function optimize(LogisticsRoute $route)
    {
        $optimizedRoute = $route->calculateOptimalRoute();
        
        return response()->json([
            'success' => true,
            'data' => $optimizedRoute
        ]);
    }

    public function getActiveRoutes()
    {
        $routes = LogisticsRoute::where('status', 'in_progress')
            ->with('getAffectedZones')
            ->get();

        return response()->json($routes);
    }

    public function start(LogisticsRoute $route)
    {
        if ($route->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Route is not in pending status'
            ]);
        }

        $route->update([
            'status' => 'in_progress',
            'start_time' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Route started successfully'
        ]);
    }

    public function complete(LogisticsRoute $route)
    {
        if ($route->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Route is not in progress'
            ]);
        }

        $route->update([
            'status' => 'completed',
            'end_time' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Route completed successfully'
        ]);
    }
} 