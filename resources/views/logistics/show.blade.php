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
                <h1 class="h2">Route Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('logistics.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back to Routes
                    </a>
                    @if($route->status === 'pending')
                        <button type="button" class="btn btn-success me-2" onclick="startRoute({{ $route->id }})">
                            <i class="fas fa-play"></i> Start Route
                        </button>
                    @endif
                    @if($route->status === 'in_progress')
                        <button type="button" class="btn btn-warning" onclick="completeRoute({{ $route->id }})">
                            <i class="fas fa-check"></i> Complete Route
                        </button>
                    @endif
                </div>
            </div>

            <!-- Route Information -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Route Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Route Name</h6>
                                    <p class="mb-3">{{ $route->name ?? 'N/A' }}</p>

                                    <h6 class="text-muted">Status</h6>
                                    <p>
                                        <span class="badge bg-{{ $route->status === 'completed' ? 'success' : ($route->status === 'in_progress' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($route->status ?? 'pending') }}
                                        </span>
                                    </p>

                                    <h6 class="text-muted">Created At</h6>
                                    <p>{{ $route->created_at ? $route->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Estimated Time</h6>
                                    <p>{{ $route->estimated_time_minutes ? $route->estimated_time_minutes . ' minutes' : 'N/A' }}</p>

                                    <h6 class="text-muted">Actual Time</h6>
                                    <p>{{ $route->actual_time_minutes ? $route->actual_time_minutes . ' minutes' : '-' }}</p>

                                    <h6 class="text-muted">Delay Status</h6>
                                    <p>
                                        <span class="badge bg-{{ ($route->delay_minutes ?? 0) > 0 ? 'danger' : 'success' }}">
                                            {{ $route->getDelayStatusAttribute() ?? 'On Time' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stops Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Route Stops</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @php
                                    $stops = json_decode($route->stops ?? '[]', true) ?? [];
                                @endphp
                                @if(count($stops) > 0)
                                    @foreach($stops as $index => $stop)
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ $stop['name'] ?? 'Unknown Stop' }}</h6>
                                                @if(isset($stop['address']))
                                                    <p class="text-muted small mb-0">{{ $stop['address'] }}</p>
                                                @endif
                                                @if(isset($stop['notes']))
                                                    <p class="small mb-0">{{ $stop['notes'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No stops defined for this route.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Intakes -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Delivery Intakes</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $deliveries = json_decode($route->deliveries ?? '[]', true) ?? [];
                            @endphp
                            @if(count($deliveries) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Stop</th>
                                                <th>Items</th>
                                                <th>Quantity</th>
                                                <th>Special Instructions</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deliveries as $delivery)
                                                <tr>
                                                    <td>{{ $delivery['stop_name'] ?? 'Unknown Stop' }}</td>
                                                    <td>
                                                        @if(isset($delivery['items']) && is_array($delivery['items']))
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach($delivery['items'] as $item)
                                                                    <li>
                                                                        <span class="badge bg-info">{{ $item['name'] ?? 'Unknown Item' }}</span>
                                                                        @if(isset($item['description']))
                                                                            <small class="text-muted d-block">{{ $item['description'] }}</small>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">No items specified</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($delivery['items']) && is_array($delivery['items']))
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach($delivery['items'] as $item)
                                                                    <li>{{ $item['quantity'] ?? 0 }} {{ $item['unit'] ?? 'units' }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($delivery['special_instructions']))
                                                            <small class="text-muted">{{ $delivery['special_instructions'] }}</small>
                                                        @else
                                                            <span class="text-muted">None</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $delivery['status'] === 'delivered' ? 'success' : ($delivery['status'] === 'in_transit' ? 'primary' : 'warning') }}">
                                                            {{ ucfirst($delivery['status'] ?? 'pending') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No delivery intakes defined for this route.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Traffic Zones -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Affected Traffic Zones</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $affectedZones = $route->getAffectedZones();
                            @endphp
                            @if($affectedZones->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($affectedZones as $zone)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $zone->name }}
                                            <span class="badge bg-info">{{ $zone->type }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">No traffic zones affected by this route.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Route Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Route Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="optimizeRoute({{ $route->id }})">
                                    <i class="fas fa-route"></i> Optimize Route
                                </button>
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fas fa-map-marked-alt"></i> View on Map
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -0.5rem;
        top: 0;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: var(--primary-color);
        border: 2px solid white;
    }

    .timeline-content {
        padding-left: 1rem;
    }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: -0.5rem;
        top: 1rem;
        bottom: 0;
        width: 2px;
        background-color: var(--border-color);
    }
</style>
@endpush

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