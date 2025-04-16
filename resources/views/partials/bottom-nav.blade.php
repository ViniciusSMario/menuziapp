<style>
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 1px solid #ddd;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 0;
        margin-top: 20px;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
        z-index: 1000;
    }

    .mobile-bottom-nav .nav-item {
        flex: 1;
        position: relative;
        text-align: center;
    }

    .mobile-bottom-nav .nav-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #333;
        text-decoration: none;
        font-size: 13px;
        padding: 8px 0;
        transition: color 0.2s ease;
        touch-action: manipulation;
    }

    .mobile-bottom-nav .nav-link.active {
        color: var(--main-color);
    }

    .mobile-bottom-nav .nav-link i {
        font-size: 18px;
        position: relative;
    }

    .cart-badge {
        position: absolute;
        top: -6px;
        right: -10px;
        font-size: 10px;
        padding: 2px 5px;
        line-height: 1;
        min-width: 16px;
        text-align: center;
    }

    @media (prefers-color-scheme: dark) {
        .mobile-bottom-nav {
            background: #1a1a1a;
            border-top-color: #333;
        }

        .mobile-bottom-nav .nav-link {
            color: #ccc;
        }

        .mobile-bottom-nav .nav-link.active {
            color: #0af;
        }
    }
</style>

<nav class="mobile-bottom-nav" aria-label="Navegação inferior">
    <div class="nav-item">
        <a href="{{ route('tenant.public.menu', $tenant->slug) }}"
            class="nav-link {{ request()->routeIs('tenant.public.menu') ? 'active' : '' }}"
            @if (request()->routeIs('tenant.public.menu')) aria-current="page" @endif>
            <i class="fas fa-store"></i>
            <span>Produtos</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="{{ route('shop', $tenant) }}" class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}"
            @if (request()->routeIs('shop')) aria-current="page" @endif>
            <i class="fas fa-shopping-cart position-relative">
                <span id="mobile-cart-count" class="badge rounded-pill bg-danger cart-badge">0</span>
            </i>
            <span>Carrinho</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="{{ route('promotions', $tenant) }}" class="nav-link {{ request()->routeIs('promotions') ? 'active' : '' }}"
            @if (request()->routeIs('promotions')) aria-current="page" @endif>
            <i class="fas fa-heart position-relative"></i>
            <span>Promoções</span>
        </a>
    </div>

    @if (Auth::user())
        <div class="nav-item">
            <a href="{{ route('meus_pedidos', $tenant->slug) }}"
                class="nav-link {{ request()->routeIs('meus_pedidos') ? 'active' : '' }}"
                @if (request()->routeIs('meus_pedidos')) aria-current="page" @endif>
                <i class="fas fa-receipt"></i>
                <span>Meus Pedidos</span>
            </a>
        </div>
    @else
        <div class="nav-item">
            <a href="{{ route('login', $tenant->slug) }}"
                class="nav-link {{ request()->routeIs('meus_pedidos') ? 'active' : '' }}"
                @if (request()->routeIs('meus_pedidos')) aria-current="page" @endif>
                <i class="fas fa-receipt"></i>
                <span>Meus Pedidos</span>
            </a>
        </div>
    @endif
</nav>
