

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
            <p class="text-muted mb-0">Welcome to SmartUrban - Your Urban Resource Management System</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="refreshData()" aria-label="Refresh Dashboard Data">
                <i class="fas fa-sync-alt me-2" aria-hidden="true"></i>Refresh
            </button>
            <button class="btn btn-outline-primary" onclick="exportData()" aria-label="Export Dashboard Data">
                <i class="fas fa-download me-2" aria-hidden="true"></i>Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card" role="region" aria-label="Total Routes Statistics">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Routes</h6>
                                        <h3 class="mb-0"><?php echo e($totalRoutes); ?></h3>
                                    </div>
                                    <div class="stat-icon bg-primary bg-opacity-10" aria-hidden="true">
                                        <i class="fas fa-route fa-2x text-primary"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up me-1" aria-hidden="true"></i>
                                        <?php echo e(round(($activeRoutes / $totalRoutes) * 100)); ?>% Active
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card" role="region" aria-label="Active Routes Statistics">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Active Routes</h6>
                                        <h3 class="mb-0"><?php echo e($activeRoutes); ?></h3>
                                    </div>
                                    <div class="stat-icon bg-success bg-opacity-10" aria-hidden="true">
                                        <i class="fas fa-check-circle fa-2x text-success"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up me-1" aria-hidden="true"></i>
                                        <?php echo e(round(($activeRoutes / $totalRoutes) * 100)); ?>% of Total
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card" role="region" aria-label="Delayed Routes Statistics">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Delayed Routes</h6>
                                        <h3 class="mb-0"><?php echo e($delayedRoutes); ?></h3>
                                    </div>
                                    <div class="stat-icon bg-warning bg-opacity-10" aria-hidden="true">
                                        <i class="fas fa-clock fa-2x text-warning"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1" aria-hidden="true"></i>
                                        <?php echo e(round(($delayedRoutes / $totalRoutes) * 100)); ?>% of Total
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="stat-card" role="region" aria-label="Utility Usage Statistics">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Utility Usage</h6>
                                        <h3 class="mb-0"><?php echo e($totalUtilityUsage); ?></h3>
                                    </div>
                                    <div class="stat-icon bg-info bg-opacity-10" aria-hidden="true">
                                        <i class="fas fa-chart-line fa-2x text-info"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="text-info">
                                        <i class="fas fa-chart-bar me-1" aria-hidden="true"></i>
                                        Last 30 Days
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Utility Usage Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Utility Usage Trends</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Select Time Period">
                            <i class="fas fa-calendar me-1" aria-hidden="true"></i>Last 30 Days
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a class="dropdown-item" href="#" role="menuitem">Last 7 Days</a></li>
                            <li><a class="dropdown-item" href="#" role="menuitem">Last 30 Days</a></li>
                            <li><a class="dropdown-item" href="#" role="menuitem">Last 90 Days</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 400px;" role="img" aria-label="Utility Usage Chart">
                        <canvas id="utilityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Zones Map -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Traffic Zones</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshMap()" aria-label="Refresh Map">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="trafficMap" style="height: 400px;" data-zones='<?php echo json_encode($trafficZones, 15, 512) ?>' role="img" aria-label="Traffic Zones Map"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Utility Usage Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Recent Utility Usage</h6>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Search utility usage">
                            <button class="btn btn-outline-secondary" type="button" aria-label="Search">
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </button>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="exportTable()" aria-label="Export Table">
                            <i class="fas fa-download me-1" aria-hidden="true"></i>Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" role="grid">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Usage</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $utilityUsage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($usage->date ? $usage->date->format('Y-m-d') : 'N/A'); ?></td>
                                    <td><?php echo e($usage->utility_type); ?></td>
                                    <td><?php echo e($usage->usage_amount); ?> <?php echo e($usage->unit); ?></td>
                                    <td>
                                        <?php
                                            $statusClass = match($usage->status) {
                                                'normal' => 'success',
                                                'high' => 'warning',
                                                'critical' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>" role="status">
                                            <?php echo e(ucfirst($usage->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" aria-label="View Details">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .stat-card {
        padding: 1.5rem;
        border-radius: 12px;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        transition: all var(--transition-speed);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all var(--transition-speed);
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .card-header {
        background-color: var(--card-bg);
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem;
        border-radius: 16px 16px 0 0 !important;
    }

    .chart-area {
        position: relative;
        height: 400px;
    }

    #trafficMap {
        border-radius: 12px;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        color: var(--text-color);
        background-color: var(--card-bg);
        border-bottom: 2px solid var(--border-color);
    }

    .table td {
        vertical-align: middle;
        color: var(--text-color);
    }

    .badge {
        padding: 0.5em 1em;
        font-weight: 500;
        border-radius: 6px;
    }

    .btn-outline-primary {
        border-color: var(--secondary-color);
        color: var(--secondary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--secondary-color);
        color: white;
    }

    .dropdown-menu {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .dropdown-item {
        color: var(--text-color);
        padding: 0.5rem 1.25rem;
    }

    .dropdown-item:hover {
        background-color: var(--light-color);
        color: var(--text-color);
    }

    .form-control {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
        border-radius: 8px;
    }

    .form-control:focus {
        background-color: var(--card-bg);
        border-color: var(--secondary-color);
        color: var(--text-color);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .input-group .btn {
        border-radius: 0 8px 8px 0;
    }

    .input-group .form-control {
        border-radius: 8px 0 0 8px;
    }

    /* Focus styles for keyboard navigation */
    .btn:focus,
    .form-control:focus,
    .dropdown-item:focus {
        outline: 2px solid var(--secondary-color);
        outline-offset: 2px;
    }

    /* High contrast mode support */
    @media (forced-colors: active) {
        .stat-card,
        .card {
            border: 1px solid CanvasText;
        }

        .btn-outline-primary {
            border: 1px solid CanvasText;
        }

        .table th {
            border-bottom: 2px solid CanvasText;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        var map = L.map('trafficMap').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add traffic zones to map
        var trafficZones = <?php echo json_encode($trafficZones, 15, 512) ?>;
        trafficZones.forEach(function(zone) {
            var marker = L.marker([zone.latitude, zone.longitude])
                .bindPopup(`
                    <div class="p-2">
                        <h6 class="mb-1">${zone.name}</h6>
                        <p class="mb-1">Congestion: ${zone.congestion_level}%</p>
                        <p class="mb-0">Status: ${zone.status}</p>
                    </div>
                `)
                .addTo(map);
        });

        // Initialize utility chart
        var ctx = document.getElementById('utilityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Utility Usage',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'var(--secondary-color)',
                    backgroundColor: 'rgba(52, 152, 219, 0.05)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'var(--border-color)'
                        },
                        ticks: {
                            color: 'var(--text-color)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'var(--text-color)'
                        }
                    }
                }
            }
        });
    });

    function refreshData() {
        // Implement data refresh logic
        location.reload();
    }

    function exportData() {
        // Implement export logic
        alert('Export functionality will be implemented');
    }

    function refreshMap() {
        // Implement map refresh logic
        location.reload();
    }

    function exportTable() {
        // Implement table export logic
        alert('Table export functionality will be implemented');
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\smart-urban-management\resources\views/dashboard.blade.php ENDPATH**/ ?>