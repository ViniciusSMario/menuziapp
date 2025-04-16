<div id="content-wrapper" class="d-flex flex-column min-vh-100">
    <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white shadow-sm px-4">
            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-light d-md-none rounded-circle me-3">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Page Title -->
            <h5 class="mb-0 fw-bold text-dark">@yield('pageTitle', 'Dashboard')</h5>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ms-auto align-items-center">

                <!-- Alerts -->
                <li class="nav-item dropdown no-arrow mx-2">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i>
                        <span class="badge bg-danger rounded-pill badge-counter">3+</span>
                    </a>
                </li>

                <div class="topbar-divider d-none d-sm-block mx-2"></div>

                <!-- User Info -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="{{ route('tenant.config.edit', $tenant)}}" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-lg-inline text-gray-800 small fw-semibold">
                            {{ Auth::user()->name ?? '--' }}
                        </span>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item ms-3">
                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger"
                        onclick="confirmLogout()">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </a>
                </li>
            </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid py-3">
            @yield('content')
        </div>
        <!-- End Page Content -->
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto bg-white py-3 shadow-sm">
        <div class="container-fluid text-center small text-muted">
            &copy; {{ $tenant->name ?? config('app.name') }} - <span id="currentYear"></span>
        </div>
    </footer>

    <!-- Logout Confirmation Modal (SweetAlert Alternative) -->
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Você deseja sair da sua conta?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, sair',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
</div>
