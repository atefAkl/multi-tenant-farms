<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام إدارة مزارع النخيل')</title>

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        .sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            right: 0;
            background: linear-gradient(135deg, #2c5530, #4a7c59);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .main-content {
            margin-right: 280px;
            transition: margin-right 0.3s ease;
        }

        .main-content.expanded {
            margin-right: 70px;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            border-radius: 15px 15px 0 0 !important;
        }

        .btn {
            border-radius: 8px;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }

        .stats-card {
            transition: transform 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="d-flex flex-column h-100">
            <!-- Logo -->
            <div class="p-3 border-bottom">
                <div class="d-flex align-items-center">
                    <i class="fas fa-seedling fa-2x text-white"></i>
                    <span class="ms-2 fw-bold" id="logo-text">نظام المزارع</span>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-grow-1 overflow-auto">
                <ul class="nav nav-pills flex-column mt-3">
                    <li class="nav-item">
                        <a href="{{ route('tenant.dashboard') }}" class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.farms.index') }}" class="nav-link {{ request()->routeIs('tenant.farms.*') ? 'active' : '' }}">
                            <i class="fas fa-tractor"></i>
                            <span>إدارة المزارع</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.blocks.index') }}" class="nav-link {{ request()->routeIs('tenant.blocks.*') ? 'active' : '' }}">
                            <i class="fas fa-cubes"></i>
                            <span>إدارة القطع</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.palm-trees.index') }}" class="nav-link {{ request()->routeIs('tenant.palm-trees.*') ? 'active' : '' }}">
                            <i class="fas fa-tree"></i>
                            <span>أشجار النخيل</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.workers.index') }}" class="nav-link {{ request()->routeIs('tenant.workers.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>إدارة العمال</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.inspections.index') }}" class="nav-link {{ request()->routeIs('tenant.inspections.*') ? 'active' : '' }}">
                            <i class="fas fa-search"></i>
                            <span>الفحوصات</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.treatments.index') }}" class="nav-link {{ request()->routeIs('tenant.treatments.*') ? 'active' : '' }}">
                            <i class="fas fa-pills"></i>
                            <span>المعالجات</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.harvests.index') }}" class="nav-link {{ request()->routeIs('tenant.harvests.*') ? 'active' : '' }}">
                            <i class="fas fa-wheat-awn"></i>
                            <span>الحصاد</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.resources.index') }}" class="nav-link {{ request()->routeIs('tenant.resources.*') ? 'active' : '' }}">
                            <i class="fas fa-boxes"></i>
                            <span>إدارة المخزون</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.invoices.index') }}" class="nav-link {{ request()->routeIs('tenant.invoices.*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice"></i>
                            <span>الفواتير</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.expenses.index') }}" class="nav-link {{ request()->routeIs('tenant.expenses.*') ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>المصروفات</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tenant.reports.index') }}" class="nav-link {{ request()->routeIs('tenant.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>التقارير</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- User Info -->
            <div class="p-3 border-top">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-light opacity-75">{{ Auth::user()->role }}</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-white text-decoration-none p-0" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('tenant.settings.index') }}"><i class="fas fa-cog"></i> الإعدادات</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary me-3" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-brand">
                    @yield('page-title', 'نظام إدارة مزارع النخيل')
                </div>

                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 text-muted">{{ tenant('name') }}</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><small>إشعار جديد</small></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const logoText = document.getElementById('logo-text');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @yield('scripts')
</body>
</html>
