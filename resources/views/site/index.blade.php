@extends('layouts.site')

@section('content')

    {{-- Header com logo e status --}}
    @include('partials.header')

    {{-- Mensagens de feedback --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">
            {{ session('success') }}
        </div>
    @endif

    @if ($tenant->bannersOrdenados()->count())
        <div class="col-12 col-md-6 offset-md-3 px-3">
            <div class="swiper mySwiper mt-3">
                <div class="swiper-wrapper">
                    @foreach ($tenant->bannersOrdenados() as $banner)
                        <div class="swiper-slide">
                            <a href="{{ $banner->link ?? '#' }}">
                                <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title ?? '' }}"
                                    class="img-fluid w-100 rounded-3"
                                    style="max-height: 250px; object-fit: contain; background-color: #f8f9fa;">
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Botões -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    @endif

    {{-- Menu de Categorias --}}
    @if ($tenant->isOpen())
        <div class="container text-center mt-3">
            <form id="search-form" class="d-flex justify-content-center mb-4" method="GET" action="#">
                <div class="input-group rounded-pill shadow-sm">
                    <input type="text" class="form-control border-0 rounded-start-pill" name="q"
                        placeholder="Buscar produto..." value="{{ request('q') }}" />
                    <button class="btn btn-dark rounded-end-pill" type="submit">
                        🔍
                    </button>
                </div>
            </form>
        </div>

        @php
            $icons = [
                'Pizza' => 'fa-pizza-slice',
                'Bebidas' => 'fa-glass-martini',
                'Lanches' => 'fa-hamburger',
                'Drinks' => 'fa-cocktail',
            ];
        @endphp

        <div class="container px-3 mt-4">
            <div class="d-flex gap-3 pb-2 flex-nowrap overflow-auto overflow-sm-visible justify-content-md-center"
                style="scroll-snap-type: x mandatory;">
                @foreach ($categories as $category)
                    @if ($category->products->isEmpty())
                        @continue
                    @endif
                    <a href="#category-{{ $category->id }}" class="text-decoration-none text-center flex-shrink-0"
                        style="width: 80px; scroll-snap-align: start;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1 shadow"
                            style="width: 60px; height: 60px; background-color: var(--main-color, #f12727); color: white;">
                            <i class="fas {{ $icons[$category->name] ?? 'fa-utensils' }}"></i>
                        </div>
                        <small class="text-dark fw-medium">{{ $category->name }}</small>
                    </a>
                @endforeach
            </div>
        </div>

        @if ($tenant->products()->where('on_promotion', true)->count())
            <div class="container text-center mt-3">
                @foreach ($categories as $category)
                    @if ($category->products->where('on_promotion', true)->isEmpty())
                        @continue
                    @else
                    <section class="mt-4">
                        <h4 class="fw-bold mb-3"><i class="fas fa-tags text-danger me-1"></i> Promoções</h4>
                        <div class="row g-3">
                            @foreach ($tenant->products()->where('on_promotion', true)->get() as $product)
                                <div class="col-6 col-md-4"
                                    onclick="openCartModal({{ $product->id }}, {{ json_encode($category->additionals) }}, '{{ $product->name }}', {{ $product->promotion_price }})">
                                    <div class="card border-0 shadow-sm">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                                alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
                                        @endif
                                        <div class="card-body p-2">
                                            <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                                            <p class="text-muted small mb-1">{{ Str::limit($product->description, 50) }}
                                            </p>
                                            <p class="mb-0">
                                                <small class="text-muted text-decoration-line-through">R$
                                                    {{ number_format($product->price, 2, ',', '.') }}</small>
                                                <br>
                                                <span class="text-success fw-bold">R$
                                                    {{ number_format($product->promotion_price, 2, ',', '.') }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                @endforeach
            </div>
        @endif

        <div id="cardapio">
            @foreach ($categories as $category)
                @if ($category->products->isEmpty())
                    @continue
                @endif
                <section id="category-{{ $category->id }}" class="container my-3">
                    <h3 class="text-center fw-bold">{{ $category->name }}</h3>

                    <div class="row">
                        @forelse ($category->products as $product)
                            @include('partials.product-card', [
                                'product' => $product,
                                'category' => $category,
                            ])
                        @empty
                            <p class="text-center text-muted">Nenhum produto disponível.</p>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>

        @if ($categories->pluck('products')->flatten()->isEmpty())
            <div class="container col-md-12">
                <div class="alert alert-warning text-center text-danger fw-bold">Nenhum produto encontrado.</div>
            </div>
        @endif
    @endif

    {{-- Botão Flutuante Carrinho --}}
    @include('partials.floating-cart')

    {{-- Modal Adicionar ao Carrinho --}}
    @include('partials.cart-modal')

    <script>
        window.CART_REDIRECT_URL = "{{ route('shop', $tenant->slug) }}";
        window.SABORES_PIZZA_URL = "{{ route('sabores_pizza', $tenant->slug) }}";
    </script>

@endsection
