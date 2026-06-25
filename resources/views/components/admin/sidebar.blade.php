{{-- Menu provided by View Composer (AppServiceProvider) --}}

<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
    {{-- Logo --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}"
           class="brand-link">
            <i class="fas fa-shield-halved brand-image opacity-80"></i>
            <span class="brand-text fw-bold">
                Control Accesos
            </span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        {{-- Usuario --}}
        <div class="user-panel">
            <div class="d-flex align-items-center px-3 py-2">
                @php
                    $u = Auth::user()->persona;
                    $iniciales = strtoupper(substr($u->primer_nombre ?? 'X', 0, 1) . substr($u->primer_apellido ?? 'X', 0, 1));
                @endphp
                <div class="img-circle elevation-2 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:35px;height:35px;background:linear-gradient(135deg,#4f46e5,#7c3aed);font-size:13px;font-weight:700;color:#fff;">
                    {{ $iniciales }}
                </div>
                <div class="info ms-2 overflow-hidden">
                    <p class="text-white mb-0 text-truncate" style="font-size:0.875rem;line-height:1.4;">
                        {{ Auth::user()->persona->primer_nombre }} {{ Auth::user()->persona->primer_apellido }}
                    </p>
                    <small class="d-block text-truncate" style="color:rgba(255,255,255,0.55);font-size:0.72rem;">
                        {{ Auth::user()->rol->nombre }}
                    </small>
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
