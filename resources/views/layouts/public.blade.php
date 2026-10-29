@extends('layouts.base')

@section('body')
    <!-- Accessibility Skip to Main Content Link -->
    <a class="visually-hidden-focusable" href="#main-content">
        Skip to main content
    </a>

    <!-- Desktop / Tablet Left Sidebar Navigation -->
    <x-sidebar />

    <!-- Mobile Top Navigation Header -->
    <x-mobile-header />

    <!-- Top Seamless Navigation Progress Bar (Threads/YouTube Style) -->
    <div id="app-nav-progress" class="app-nav-progress" aria-hidden="true"></div>

    <!-- Main Content Area -->
    <main id="main-content" data-app-content class="main-layout flex-grow-1 min-vh-100 d-flex flex-column">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <!-- Mobile Bottom Navigation Bar (Supports all roles including Admin) -->
    <x-mobile-nav />

    <!-- Mobile Extended Drawer Menu (Threads-inspired Offcanvas) -->
    <x-mobile-drawer />

    <!-- Global Bootstrap Confirmation Modal -->
    <x-confirm-modal />

    <!-- Global Bootstrap Toast Notifications -->
    <x-toast />
@endsection
