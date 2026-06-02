{{-- Menu provided by View Composer (AppServiceProvider) --}}

<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
    {{-- Logo --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}"
           class="brand-link">
            <i class="fas fa-book-open brand-image opacity-75 ms-3 me-2"></i>
            <span class="brand-text fw-light">
                Control Accesos
            </span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        {{-- Usuario --}}
        <div class="px-3 py-3 border-bottom border-secondary">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-circle fa-2x text-secondary"></i>
                <div class="ms-2">
                    <div class="text-white small fw-light">
                        {{ Auth::user()->persona->primer_nombre }}
                        {{ Auth::user()->persona->primer_apellido }}
                    </div>
                    <span class="badge text-bg-success mt-1">
                        {{ Auth::user()->rol->nombre }}
                    </span>
                </div>
            </div>
        </div>
        {{-- Menú --}}
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="menu"
                data-accordion="false">

                <li class="nav-header small text-uppercase">
                    <span class="opacity-50">
                        Menú principal
                    </span>
                </li>
                @foreach($menu as $item)
                <li class="nav-item {{ isset($item['children']) && request()->routeIs($item['match']) ? 'menu-open' : '' }}">

                    {{-- Si tiene hijos: toggle; si no, enlace directo --}}
                    @if(isset($item['children']))
                    <a href="#" class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                        <p>
                            {{ $item['label'] }}
                            <i class="nav-arrow fas fa-angle-right ms-auto"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        @foreach($item['children'] as $child)
                        <li class="nav-item">
                            <a href="{{ Route::has($child['route']) ? route($child['route']) : '#' }}"
                               class="nav-link {{ request()->routeIs($child['match']) ? 'active' : '' }}">
                                <i class="nav-icon {{ $child['icon'] }}"></i>
                                <p>{{ $child['label'] }}</p>
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    @else
                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                        <p>{{ $item['label'] }}</p>
                    </a>
                    @endif
                </li>
                @endforeach
                {{-- Logout --}}
                <li class="nav-item mt-3">
                    <a href="#"
                       class="nav-link text-danger"
                       onclick="event.preventDefault();
                                 document.getElementById('form-logout-sidebar').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Cerrar sesión</p>
                    </a>
                    <form id="form-logout-sidebar"
                          method="POST"
                          action="{{ route('admin.logout') }}"
                          class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
