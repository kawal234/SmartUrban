@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('logistics.index') }}">
                            Logistics Routes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('utilities.index') }}">
                            Utility Usage
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Logistics Routes Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('logistics.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> New Route
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('logistics.index') }}" method="GET" class="row g-3">
                        @csrf
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="delay" class="form-label">Delay Status</label>
                            <select class="form-select" id="delay" name="delay">
                                <option value="">All</option>
                                <option value="on_time" {{ request('delay') == 'on_time' ? 'selected' : '' }}>On Time</option>
                                <option value="delayed" {{ request('delay') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date" class="form-label">Date Range</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Routes List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Details</th>
                                    <th>Route Information</th>
                                    <th>Timing</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routes as $route)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong class="text-primary">{{ $route->name }}</strong>
                                            <small class="text-muted">Order ID: #{{ $route->id }}</small>
                                            <div class="mt-1">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-box"></i> {{ count(json_decode($route->stops, true) ?? []) }} Stops
                                                </span>
                                                <span class="badge bg-secondary ms-1">
                                                    <i class="fas fa-map-marker-alt"></i> {{ count(json_decode($route->traffic_zones_crossed, true) ?? []) }} Zones
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="mb-1">
                                                <i class="fas fa-route text-primary"></i>
                                                <span class="ms-1">
                                                    @php
                                                        $stops = json_decode($route->stops, true) ?? [];
                                                        $stopNames = array_map(function($stop) {
                                                            return $stop['name'] ?? 'Unknown Stop';
                                                        }, $stops);
                                                    @endphp
                                                    {{ implode(' → ', $stopNames) }}
                                                </span>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-clock"></i> Created: {{ $route->created_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="mb-1">
                                                <span class="text-muted">Estimated:</span>
                                                <span class="ms-1">{{ $route->estimated_time_minutes }} min</span>
                                            </div>
                                            <div>
                                                <span class="text-muted">Actual:</span>
                                                <span class="ms-1">{{ $route->actual_time_minutes ? $route->actual_time_minutes . ' min' : '-' }}</span>
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge bg-{{ $route->delay_minutes > 0 ? 'danger' : 'success' }}">
                                                    <i class="fas fa-{{ $route->delay_minutes > 0 ? 'exclamation-triangle' : 'check-circle' }}"></i>
                                                    {{ $route->delay_minutes > 0 ? $route->delay_minutes . ' min delay' : 'On Time' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-{{ $route->status === 'completed' ? 'success' : ($route->status === 'in_progress' ? 'primary' : 'warning') }}">
                                                {{ ucfirst($route->status) }}
                                            </span>
                                            @if($route->start_time)
                                                <small class="text-muted mt-1">
                                                    <i class="fas fa-play"></i> Started: {{ $route->start_time->format('H:i') }}
                                                </small>
                                            @endif
                                            @if($route->end_time)
                                                <small class="text-muted mt-1">
                                                    <i class="fas fa-stop"></i> Completed: {{ $route->end_time->format('H:i') }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('logistics.show', $route) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="optimizeRoute({{ $route->id }})" title="Optimize Route">
                                                <i class="fas fa-route"></i>
                                            </button>
                                            @if($route->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-success" onclick="startRoute({{ $route->id }})" title="Start Route">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                            @if($route->status === 'in_progress')
                                                <button type="button" class="btn btn-sm btn-warning" onclick="completeRoute({{ $route->id }})" title="Complete Route">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $routes->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    function optimizeRoute(routeId) {
        fetch(`/logistics/${routeId}/optimize`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Route optimized successfully!');
                location.reload();
            } else {
                alert('Failed to optimize route.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while optimizing the route.');
        });
    }

    function startRoute(routeId) {
        fetch(`/logistics/${routeId}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to start route.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while starting the route.');
        });
    }

    function completeRoute(routeId) {
        fetch(`/logistics/${routeId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to complete route.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while completing the route.');
        });
    }
</script>
@endpush
@endsection 