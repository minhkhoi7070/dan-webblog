<nav class="navbar navbar-expand-sm navbar-light bg-body border-bottom">
    <div class="container-xl">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <x-application-logo style="height: 36px;" />
        </a>

        <!-- Hamburger Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#breezeNavbar" aria-controls="breezeNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="breezeNavbar">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                <li class="nav-item">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </li>
            </ul>

            <!-- Settings Dropdown -->
            <ul class="navbar-nav ms-auto align-items-sm-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-body fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
