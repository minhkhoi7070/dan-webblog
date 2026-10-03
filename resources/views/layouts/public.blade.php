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

    <!-- Main Content Area -->
    <main id="main-content" class="main-layout flex-grow-1 w-100 min-vh-100 d-flex flex-column">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <!-- Mobile Bottom Navigation Bar (Hidden on Admin pages to avoid covering management tools) -->
    @if(!request()->routeIs('admin.*'))
        <x-mobile-nav />
    @endif

    <!-- Global Bootstrap Confirmation Modal -->
    <x-confirm-modal />

    <!-- Global Bootstrap Toast Notifications -->
    <x-toast />
@endsection
