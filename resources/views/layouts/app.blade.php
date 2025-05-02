<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SmartUrban - Urban Resource Management</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <!-- App CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --dark-color: #34495e;
            --light-color: #ecf0f1;
            --text-color: #2c3e50;
            --bg-color: #ffffff;
            --card-bg: #ffffff;
            --border-color: rgba(0,0,0,0.1);
            --sidebar-width: 280px;
            --header-height: 70px;
            --transition-speed: 0.3s;
        }

        [data-theme="dark"] {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --dark-color: #ecf0f1;
            --light-color: #2c3e50;
            --text-color: #ecf0f1;
            --bg-color: #1a1a1a;
            --card-bg: #2c3e50;
            --border-color: rgba(255,255,255,0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all var(--transition-speed) ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            height: var(--header-height);
            background: var(--card-bg);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .wrapper {
            display: flex;
            margin-top: var(--header-height);
        }

        #sidebar {
            width: var(--sidebar-width);
            background: rgba(44, 62, 80, 0.9);
            backdrop-filter: blur(8px);
            color: white;
            position: fixed;
            top: var(--header-height);
            bottom: 0;
            overflow-y: auto;
            transition: margin-left var(--transition-speed);
            z-index: 999;
        }

        #sidebar.active {
            margin-left: -280px;
        }

        #content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: margin-left var(--transition-speed);
        }

        #content.active {
            margin-left: 0;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .components {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .components li {
            padding: 1rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .components li.active,
        .components li:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: var(--secondary-color);
        }

        .components a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-toggle-theme {
            margin-left: auto;
            margin-right: 1rem;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: -280px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
            }

            #content.active {
                margin-left: var(--sidebar-width);
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <nav class="navbar d-flex align-items-center justify-content-between px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <i class="fas fa-city text-primary"></i>
            <span class="fw-bold">SmartUrban</span>
        </a>

        <div class="d-flex align-items-center gap-4">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <!-- Dark Mode Toggle -->
            <button id="theme-toggle" class="btn btn-outline-primary btn-sm btn-toggle-theme">
                <i class="fas fa-moon"></i>
            </button>

            <!-- Auth Dropdown -->
            @auth
                <div class="dropdown">
                    <a class="btn btn-light dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
            @endauth
        </div>
    </nav>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-city"></i> SmartUrban</h3>
            </div>
            <ul class="components">
                <li class="{{ request()->routeIs('logistics.*') ? 'active' : '' }}">
                    <a href="{{ route('logistics.index') }}">
                        <i class="fas fa-truck"></i> Logistics
                    </a>
                </li>
                <li class="{{ request()->routeIs('utilities.*') ? 'active' : '' }}">
                    <a href="{{ route('utilities.index') }}">
                        <i class="fas fa-bolt"></i> Utilities
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div id="content">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.getElementById('theme-toggle');
            themeToggle.addEventListener('click', () => {
                const html = document.documentElement;
                html.dataset.theme = html.dataset.theme === 'dark' ? 'light' : 'dark';
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
