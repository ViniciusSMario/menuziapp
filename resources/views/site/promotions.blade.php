@extends('layouts.site')

@section('title', 'Promoções')

@section('content')
    @include('partials.header')
    <div class="container py-5">
        <h1 class="text-center fw-bold mb-5 display-5 text-danger-emphasis">🎉 Ofertas Imperdíveis</h1>

        @if ($products->count())
            <div class="row g-4">
                @foreach ($products as $product)
                    <div class="col-6 col-sm-6 col-md-4">
                        <div class="card h-100 border-0 shadow-lg rounded-4 position-relative overflow-hidden">

                            <span
                                class="position-absolute top-0 start-0 bg-main text-white px-3 py-1 rounded-end z-2 shadow-sm">
                                Promoção
                            </span>

                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top rounded-top-4"
                                    alt="{{ $product->name }}" style="object-fit: cover; height: 220px;">
                            @endif

                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fw-bold text-dark">{{ $product->name }}</h5>
                                    <p class="text-muted small">{{ $product->description }}</p>

                                    <div class="mb-2">
                                        <span class="text-muted text-decoration-line-through small">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </span><br>
                                        <span class="text-success fw-bold fs-5">
                                            R$ {{ number_format($product->promotion_price, 2, ',', '.') }}
                                        </span>
                                        <p class="small text-success mb-0">
                                            Você economiza
                                            <strong>R$
                                                {{ number_format($product->price - $product->promotion_price, 2, ',', '.') }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-white border-top-0 pb-4 px-3">
                                @if ($tenant->isOpen())
                                    <button class="btn btn-main w-100 rounded-pill"
                                        onclick="openCartModal('{{ $product->id }}', [], '{{ $product->name }}', '{{ $product->promotion_price }}')">
                                        <i class="fas fa-cart-plus me-1"></i> Adicionar ao Carrinho
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted">Nenhum produto em promoção no momento.</p>
        @endif

        {{-- Botão Flutuante Carrinho --}}
        @include('partials.floating-cart')

        {{-- Modal Adicionar ao Carrinho --}}
        @include('partials.cart-modal')

        <script>
            window.CART_REDIRECT_URL = "{{ route('shop', $tenant->slug) }}";
            window.SABORES_PIZZA_URL = "{{ route('sabores_pizza', $tenant->slug) }}";
        </script>
    </div>
@endsection
