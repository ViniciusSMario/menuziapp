<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ $tenant->logo ? asset('storage/' . $tenant->logo) : asset('images/MenuziAppLogo.png') }}" type="image/x-icon">
    <title>@yield('title', 'Monitor de Cozinha - ' . ($tenant->name ?? 'MenuziApp'))</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- FontAwesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- Toastr --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Estilo customizado --}}
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding-top: 70px; /* altura do topo fixo */
        }

        .topbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 999;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            height: 70px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar .left-icons i {
            font-size: 20px;
            margin-right: 20px;
            color: #6c757d;
            cursor: pointer;
        }

        .topbar .left-icons i.active {
            color: #0d6efd;
        }

        .topbar .brand {
            font-weight: bold;
            font-size: 20px;
            color: #dc3545;
        }

        .topbar .right-info {
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .topbar .right-info span {
            color: #6c757d;
        }

        .topbar .right-info .user {
            font-weight: bold;
        }
    </style>

    @stack('styles')
</head>
<body>

    <div class="topbar">
        <div class="d-flex align-items-center gap-4">
            <span class="brand">
                <img src="{{ asset('storage/' . ($tenant->logo ?? 'default-logo.png')) }}"  width="50px" alt="logo" class="rounded-3">
                {{ $tenant->name }}
            </span>
            <div class="left-icons d-flex align-items-center">
                <a href="{{ route('tenant.dashboard', ['tenant' => $tenant->slug]) }}">
                    <i class="fas fa-chart-bar" title="Dashboard"></i>
                </a>
                <a href="{{ route('tenant.pdv.index', ['tenant' => $tenant->slug]) }}">
                    <i class="fas fa-ticket-alt" title="Pedidos"></i>
                </a>
                <a href="{{ route('tenant.products.index', ['tenant' => $tenant->slug]) }}">
                    <i class="fas fa-utensils" title="Produtos"></i>
                </a>
                <a href="{{ route('tenant.tables.index', ['tenant' => $tenant->slug]) }}">
                    <i class="fas fa-table" title="Mesas"></i>
                </a>
                <a href="{{ route('tenant.config.edit', ['tenant' => $tenant->slug]) }}">
                    <i class="fas fa-cogs" title="Configurações"></i>
                </a>
            </div>
        </div>

        <div class="right-info">
            <a href="{{ route('tenant.pdv.index', ['tenant' => $tenant->slug]) }}" class="fw-bold text-dark text-decoration-none">{{ $tenant->name }}</a>
            <span>{{ strtoupper(now()->translatedFormat('l, d M')) }}</span>
            <span>{{ now()->format('H:i') }}</span>
            <span class="user"><i class="fas fa-user"></i> {{ Auth::user()->name ?? 'Usuário' }}</span>
        </div>
    </div>

    <main class="container-fluid">
        @yield('content')
    </main>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>

</html>
