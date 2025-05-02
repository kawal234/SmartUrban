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
                <h1 class="h2">Add New Utility Usage Record</h1>
            </div>

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Usage Details</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('utilities.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="utility_type" class="form-label">Utility Type</label>
                                    <select class="form-select" id="utility_type" name="utility_type" required>
                                        <option value="">Select Type</option>
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
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" id="usage_amount" name="usage_amount" required>
                                        <span class="input-group-text" id="unit_display">-</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="peak_hours" class="form-label">Peak Hours</label>
                                    <input type="text" class="form-control" id="peak_hours" name="peak_hours" placeholder="e.g., 9:00-17:00" required>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Save Record</button>
                                    <a href="{{ route('utilities.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const utilityType = document.getElementById('utility_type');
        const unitDisplay = document.getElementById('unit_display');

        // Set today's date as default
        document.getElementById('date').valueAsDate = new Date();

        // Update unit display based on utility type
        utilityType.addEventListener('change', function() {
            const units = {
                'electricity': 'kWh',
                'water': 'm³',
                'gas': 'm³'
            };
            unitDisplay.textContent = units[this.value] || '-';
        });
    });
</script>
@endpush
@endsection 