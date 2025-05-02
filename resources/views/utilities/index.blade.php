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
                        <a class="nav-link" href="{{ route('logistics.index') }}">
                            Logistics Routes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('utilities.index') }}">
                            Utility Usage
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Utility Usage Monitoring</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUsageModal">
                        <i class="bi bi-plus-circle"></i> Add Usage Record
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label for="utility_type" class="form-label">Utility Type</label>
                            <select class="form-select" id="utility_type" name="utility_type">
                                <option value="">All Types</option>
                                <option value="electricity">Electricity</option>
                                <option value="water">Water</option>
                                <option value="gas">Gas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Usage Trends</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="usageChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Usage by Type</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="typeChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Records Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Usage Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Utility Type</th>
                                    <th>Usage Amount</th>
                                    <th>Peak Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usages as $usage)
                                <tr>
                                    <td>{{ $usage->date->format('Y-m-d') }}</td>
                                    <td>{{ ucfirst($usage->utility_type) }}</td>
                                    <td>{{ $usage->usage_amount }} {{ $usage->unit }}</td>
                                    <td>{{ $usage->peak_hours }}</td>
                                    <td>
                                        <span class="badge bg-{{ $usage->status === 'normal' ? 'success' : 'warning' }}">
                                            {{ ucfirst($usage->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editUsage({{ $usage->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUsage({{ $usage->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $usages->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Usage Modal -->
<div class="modal fade" id="addUsageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Usage Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('utilities.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="utility_type" class="form-label">Utility Type</label>
                        <select class="form-select" id="utility_type" name="utility_type" required>
                            <option value="electricity">Electricity</option>
                            <option value="water">Water</option>
                            <option value="gas">Gas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="usage_amount" class="form-label">Usage Amount</label>
                        <input type="number" step="0.01" class="form-control" id="usage_amount" name="usage_amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="peak_hours" class="form-label">Peak Hours</label>
                        <input type="text" class="form-control" id="peak_hours" name="peak_hours" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        // Usage Trends Chart
        const usageCtx = document.getElementById('usageChart').getContext('2d');
        new Chart(usageCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($usageTrends['dates']) !!},
                datasets: [{
                    label: 'Usage',
                    data: {!! json_encode($usageTrends['values']) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Usage Trend'
                    }
                }
            }
        });

        // Usage by Type Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($usageByType['types']) !!},
                datasets: [{
                    data: {!! json_encode($usageByType['values']) !!},
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Usage Distribution by Type'
                    }
                }
            }
        });
    });

    // Filter form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.href = '{{ route('utilities.index') }}?' + params.toString();
    });

    function editUsage(id) {
        // Implement edit functionality
        console.log('Edit usage:', id);
    }

    function deleteUsage(id) {
        if (confirm('Are you sure you want to delete this usage record?')) {
            fetch(`/utilities/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        }
    }
</script>
@endpush
@endsection 