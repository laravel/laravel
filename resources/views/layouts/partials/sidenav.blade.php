<nav>
    <div id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white" data-simplebar>
        <div class="sidebar-inner px-2 pt-3">
            <ul class="nav flex-column pt-3 pt-md-0">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link" wire:navigate>
                        <span class="sidebar-icon">
                            <img src="/assets/images/brand/light.svg" height="20" width="20" alt="Volt Logo">
                        </span>
                        <span class="mt-1 ms-1 sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('countries.index') }}" class="nav-link" wire:navigate>
                        <span class="sidebar-icon"><i class="fas fa-globe"></i></span>
                        <span class="sidebar-text">Countries</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    </nav>
