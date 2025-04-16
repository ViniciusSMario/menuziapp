<!-- Sidebar -->
<ul class="navbar-nav bg-main sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('tenant.dashboard', tenant()->slug) }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <img src="{{ $tenant->logo ? asset('storage/' . $tenant->logo) : asset('images/MenuziAppLogo.png') }}" width="50px" alt="logo" class="rounded-circle">
        </div>
        <div class="sidebar-brand-text mx-3">{{ $tenant->name ?? '' }}</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- PDV -->
    <li class="nav-item {{ Request::is('tenant.pdv.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.pdv.index', tenant()->slug) }}">
            <i class="fas fa-cash-register"></i>
            <span>PDV</span>
        </a>
    </li>

    <!-- Dashboard -->
    <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.dashboard', tenant()->slug) }}">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard e Relatórios</span>
        </a>
    </li>

    <!-- Categorias -->
    <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/categories*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCategories"
            aria-expanded="{{ Request::is('admin/' . tenant()->slug . '/categories*') ? 'true' : 'false' }}" aria-controls="collapseCategories">
            <i class="fas fa-tags"></i>
            <span>Categorias</span>
        </a>
        <div id="collapseCategories" class="collapse {{ Request::is('admin/' . tenant()->slug . '/categories*') ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('tenant.categories.index', tenant()->slug) }}">Listar Categorias</a>
                <a class="collapse-item" href="{{ route('tenant.categories.create', tenant()->slug) }}">Adicionar Categoria</a>
                <a class="collapse-item" href="{{ route('tenant.categories.import.form', tenant()->slug) }}">Importar Categorias</a>
            </div>
        </div>
    </li>

    <!-- Produtos -->
    <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/products*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProducts"
            aria-expanded="{{ Request::is('admin/' . tenant()->slug . '/products*') ? 'true' : 'false' }}" aria-controls="collapseProducts">
            <i class="fas fa-box"></i>
            <span>Produtos</span>
        </a>
        <div id="collapseProducts" class="collapse {{ Request::is('admin/' . tenant()->slug . '/products*') ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('tenant.products.index', tenant()->slug) }}">Listar Produtos</a>
                <a class="collapse-item" href="{{ route('tenant.products.create', tenant()->slug) }}">Adicionar Produto</a>
                <a class="collapse-item" href="{{ route('tenant.products.import.form', tenant()->slug) }}">Importar Produtos</a>
            </div>
        </div>
    </li>

    <!-- Adicionais -->
    <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/additionals*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdicionais"
            aria-expanded="{{ Request::is('admin/' . tenant()->slug . '/adicionais*') ? 'true' : 'false' }}" aria-controls="collapseAdicionais">
            <i class="fas fa-plus-circle"></i>
            <span>Adicionais</span>
        </a>
        <div id="collapseAdicionais" class="collapse {{ Request::is('admin/' . tenant()->slug . '/additionals*') ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('tenant.additionals.index', tenant()->slug) }}">Listar Adicionais</a>
                <a class="collapse-item" href="{{ route('tenant.additionals.create', tenant()->slug) }}">Novo Adicional</a>
                <a class="collapse-item" href="{{ route('tenant.additionals.import.form', tenant()->slug) }}">Importar Adicionais</a>
            </div>
        </div>
    </li>

    <!-- Mesas -->
    <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/tables*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.tables.index', tenant()->slug) }}">
            <i class="fas fa-table"></i>
            <span>Mesas</span>
        </a>
    </li>
    <!-- Mesas -->
    {{-- <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/garcons*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.garcons.index', tenant()->slug) }}">
            <i class="fas fa-table"></i>
            <span>Garçons</span>
        </a>
    </li> --}}
    <!-- Fretes -->
    <li class="nav-item {{ Request::is('admin/ ' . tenant()->slug . '/neighborhoods*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.neighborhoods.index', tenant()->slug) }}">
            <i class="fas fa-map-marker-alt"></i>
            <span>Fretes</span>
        </a>
    </li>

    <!-- Cupons -->
    <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/coupons*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.coupons.index', tenant()->slug) }}">
            <i class="fas fa-ticket-alt"></i>
            <span>Cupons</span>
        </a>
    </li>

    <!-- Pedidos -->
    {{-- <li class="nav-item {{ Request::is('admin/orders*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.orders.index', tenant()->slug) }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Pedidos</span>
        </a>
    </li> --}}

    <li class="nav-item {{ Request::is('admin/' . tenant()->slug . '/config*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tenant.config.edit', tenant()->slug) }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Minha Empresa</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>