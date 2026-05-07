<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Simple POS') - Simple POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { min-height: 100vh; background-color: #f8f9fa; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #343a40; width: 250px; flex-shrink: 0; }
        .sidebar .nav-link { color: #adb5bd; padding: .5rem 1rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,.1); border-radius: .25rem; }
        .sidebar .nav-link i { width: 20px; }
        .sidebar .sidebar-heading { color: #6c757d; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; padding: .5rem 1rem; }
        .content-wrapper { flex: 1; overflow-x: auto; }
        .main-wrapper { display: flex; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-dark bg-dark navbar-expand-lg px-3" style="height:56px;">
    <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">Simple POS</a>
    <div class="ms-auto d-flex align-items-center">
        <span class="text-light me-3 small">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> {{ __('auth.logout') }}
            </button>
        </form>
    </div>
</nav>

<div class="main-wrapper">
    {{-- Sidebar --}}
    <div class="sidebar py-3">
        <ul class="nav flex-column px-2">

            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> {{ __('nav.dashboard') }}
                </a>
            </li>

            @if(Auth::user()->hasRole(['superadmin','owner','admin']))
            <li class="mt-2">
                <span class="sidebar-heading">{{ __('nav.master_product') }}</span>
            </li>

            <li class="nav-item">
                <a href="{{ route('products.index') }}"
                   class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> {{ __('nav.products') }}
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('product-categories.index') }}"
                   class="nav-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> {{ __('nav.product_categories') }}
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('product-units.index') }}"
                   class="nav-link {{ request()->routeIs('product-units.*') ? 'active' : '' }}">
                    <i class="bi bi-rulers"></i> {{ __('nav.product_units') }}
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('customers.index') }}"
                   class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> {{ __('nav.customers') }}
                </a>
            </li>
            @endif

            @if(Auth::user()->hasRole(['superadmin','owner']))
            <li class="mt-2">
                <span class="sidebar-heading">{{ __('nav.outlets') }}</span>
            </li>
            <li class="nav-item">
                <a href="{{ route('outlets.index') }}"
                   class="nav-link {{ request()->routeIs('outlets.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> {{ __('nav.outlets') }}
                </a>
            </li>
            @endif

            @if(Auth::user()->hasRole('superadmin'))
            <li class="mt-2">
                <span class="sidebar-heading">Admin</span>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}"
                   class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> {{ __('nav.users') }}
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('roles.index') }}"
                   class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> {{ __('nav.roles') }}
                </a>
            </li>
            @endif

        </ul>
    </div>

    {{-- Content --}}
    <div class="content-wrapper p-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

{{-- Footer --}}
<footer class="bg-dark text-center text-light py-2 small">
    &copy; {{ date('Y') }} Simple POS. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
