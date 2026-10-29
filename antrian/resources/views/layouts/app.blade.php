<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Antrian Puskesmas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @stack('styles')
    <style>
        body { background: #f4f6f9; }
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            color: #fff;
        }
        .sidebar .nav-link { color: rgba(255,255,255,.8); }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; }
        .sidebar .brand {
            display: block;
            padding: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            background: rgba(0,0,0,.1);
            text-decoration: none;
            color: #fff;
        }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #fff;
            color: #0d6efd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="row g-0">
    <div class="col-auto">
        <div class="sidebar d-flex flex-column">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <i class="bi bi-hospital"></i> Antrian Puskesmas
            </a>
            @if(auth()->user()->isAdmin())
            <ul class="nav nav-pills flex-column px-2 py-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.klaster*') ? 'active' : '' }}" href="{{ route('admin.klaster') }}">
                        <i class="bi bi-diagram-3"></i> Klaster
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.polis*') ? 'active' : '' }}" href="{{ route('admin.polis') }}">
                        <i class="bi bi-clipboard2-pulse"></i> Layanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.loket*') ? 'active' : '' }}" href="{{ route('admin.loket') }}">
                        <i class="bi bi-box2"></i> Loket
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.user*') ? 'active' : '' }}" href="{{ route('admin.user') }}">
                        <i class="bi bi-people"></i> Petugas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.setting*') ? 'active' : '' }}" href="{{ route('admin.setting') }}">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}" href="{{ route('admin.laporan') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                    </a>
                </li>
            </ul>
            @endif
            <div class="mt-auto p-3">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="btn btn-sm btn-light w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h4 mb-0">@yield('pageTitle', 'Panel')</h1>
                    <small class="text-muted">@yield('pageSubtitle', '')</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">{{ auth()->user()->name }}</span>
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>