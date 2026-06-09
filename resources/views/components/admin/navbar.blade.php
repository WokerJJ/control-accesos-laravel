<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="far fa-user-circle me-1"></i>
                    {{ Auth::user()->persona->nombre_completo }}
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-item">
                        <i class="fas fa-id-badge me-2 text-muted"></i>
                        <small class="text-muted">{{ Auth::user()->rol->nombre }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                        </button>
                    </form>
                </div>
            </li>
        </ul>

    </div>
</nav>
