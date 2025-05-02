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
                <h1 class="h2">Create New Logistics Route</h1>
            </div>

            <div class="row">
                <!-- Map Section -->
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Route Planning</h5>
                        </div>
                        <div class="card-body">
                            <div id="routeMap" style="height: 500px;"></div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary" onclick="addStop()">
                                    <i class="fas fa-plus-circle"></i> Add Stop
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="clearRoute()">
                                    <i class="fas fa-trash"></i> Clear Route
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Route Details</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('logistics.store') }}" method="POST" id="routeForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Route Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="estimated_time" class="form-label">Estimated Time (minutes)</label>
                                    <input type="number" class="form-control @error('estimated_time_minutes') is-invalid @enderror" id="estimated_time" name="estimated_time_minutes" value="{{ old('estimated_time_minutes') }}" required min="1">
                                    @error('estimated_time_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Selected Stops</label>
                                    <div id="stopsList" class="list-group mb-3">
                                        <!-- Stops will be added here dynamically -->
                                    </div>
                                    @error('stops')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Affected Traffic Zones</label>
                                    <div id="zonesList" class="list-group">
                                        @foreach($zones as $zone)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="traffic_zones_crossed[]" value="{{ $zone->id }}" id="zone{{ $zone->id }}" {{ in_array($zone->id, old('traffic_zones_crossed', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="zone{{ $zone->id }}">
                                                {{ $zone->name }} ({{ $zone->congestion_level }}% congestion)
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('traffic_zones_crossed')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <input type="hidden" name="stops" id="stopsInput" value="{{ old('stops') }}">
                                <button type="submit" class="btn btn-primary w-100">Create Route</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .stop-marker {
        background-color: #007bff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        border: 2px solid white;
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    .stop-marker:hover {
        background-color: #0056b3;
    }
    .stop-number {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: bold;
        font-size: 12px;
    }
    .route-line {
        stroke: #007bff;
        stroke-width: 3;
        stroke-dasharray: 5, 5;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    let map;
    let markers = [];
    let polyline;
    let stops = [];
    let stopNames = new Set();
    let isAddingStop = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        map = L.map('routeMap').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Load existing stops if any
        const existingStops = document.getElementById('stopsInput').value;
        if (existingStops) {
            try {
                stops = JSON.parse(existingStops);
                stops.forEach((stop, index) => {
                    stopNames.add(stop.name);
                    addStopToMap({lat: stop.lat, lng: stop.lng}, stop.name, index + 1);
                });
            } catch (e) {
                console.error('Error parsing existing stops:', e);
            }
        }
    });

    function addStop() {
        if (isAddingStop) {
            alert('Please complete adding the current stop first.');
            return;
        }

        isAddingStop = true;
        map.once('click', function(e) {
            const stopName = prompt('Enter stop name:');
            if (stopName) {
                if (stopNames.has(stopName)) {
                    alert('Stop name already exists. Please use a different name.');
                    isAddingStop = false;
                    return;
                }
                addStopToMap(e.latlng, stopName, stops.length + 1);
                stopNames.add(stopName);
            }
            isAddingStop = false;
        });
    }

    function addStopToMap(latlng, name, number) {
        // Create custom marker with number
        const marker = L.marker(latlng, {
            draggable: true,
            icon: L.divIcon({
                className: 'stop-marker',
                html: `<div class="stop-marker"><span class="stop-number">${number}</span></div>`
            })
        }).addTo(map);

        // Add popup with stop name
        marker.bindPopup(`
            <div class="text-center">
                <strong>Stop ${number}</strong><br>
                ${name}<br>
                <small>${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}</small>
            </div>
        `).openPopup();

        // Handle marker drag
        marker.on('dragend', function(e) {
            const index = markers.indexOf(marker);
            if (index !== -1) {
                stops[index].lat = e.target.getLatLng().lat;
                stops[index].lng = e.target.getLatLng().lng;
                updateStopsInput();
                updateRouteLine();
            }
        });

        // Add to markers array
        markers.push(marker);

        // Add to stops array
        stops.push({
            name: name,
            lat: latlng.lat,
            lng: latlng.lng,
            number: number
        });

        // Update stops input
        updateStopsInput();

        // Update stops list
        updateStopsList();

        // Update route line
        updateRouteLine();
    }

    function updateStopsInput() {
        document.getElementById('stopsInput').value = JSON.stringify(stops);
    }

    function updateStopsList() {
        const stopsList = document.getElementById('stopsList');
        stopsList.innerHTML = '';

        stops.forEach((stop, index) => {
            const stopItem = document.createElement('div');
            stopItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            stopItem.innerHTML = `
                <div>
                    <strong>Stop ${stop.number}: ${stop.name}</strong>
                    <small class="d-block text-muted">${stop.lat.toFixed(6)}, ${stop.lng.toFixed(6)}</small>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="moveStop(${index})">
                        <i class="fas fa-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeStop(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            stopsList.appendChild(stopItem);
        });
    }

    function updateRouteLine() {
        if (polyline) {
            map.removeLayer(polyline);
        }

        if (stops.length >= 2) {
            const latlngs = stops.map(stop => [stop.lat, stop.lng]);
            polyline = L.polyline(latlngs, {
                color: '#007bff',
                weight: 3,
                dashArray: '5, 5'
            }).addTo(map);
            map.fitBounds(polyline.getBounds());
        }
    }

    function moveStop(index) {
        const marker = markers[index];
        marker.openPopup();
        map.setView(marker.getLatLng(), map.getZoom());
    }

    function removeStop(index) {
        const stop = stops[index];
        if (confirm(`Are you sure you want to remove stop ${stop.number}: ${stop.name}?`)) {
            stopNames.delete(stop.name);
            stops.splice(index, 1);
            markers[index].remove();
            markers.splice(index, 1);
            
            // Update stop numbers
            stops.forEach((stop, i) => {
                stop.number = i + 1;
                markers[i].setIcon(L.divIcon({
                    className: 'stop-marker',
                    html: `<div class="stop-marker"><span class="stop-number">${i + 1}</span></div>`
                }));
            });

            updateStopsInput();
            updateStopsList();
            updateRouteLine();
        }
    }

    function clearRoute() {
        if (stops.length === 0) {
            alert('No stops to clear.');
            return;
        }

        if (confirm('Are you sure you want to clear all stops?')) {
            stops = [];
            stopNames.clear();
            markers.forEach(marker => marker.remove());
            markers = [];
            if (polyline) {
                map.removeLayer(polyline);
            }
            updateStopsInput();
            updateStopsList();
        }
    }

    // Form validation
    document.getElementById('routeForm').addEventListener('submit', function(e) {
        if (stops.length < 2) {
            e.preventDefault();
            alert('Please add at least two stops to create a route.');
        }
    });
</script>
@endpush
@endsection 