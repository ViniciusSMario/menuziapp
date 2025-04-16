@extends('layouts.site')

@section('title', 'Meus Pedidos')

@section('content')
    {{-- Header com logo e status --}}
    @include('partials.header')
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10">
                <a href="{{ route('tenant.public.menu', $tenant->slug) }}"
                    class="btn btn-outline-secondary mb-1 rounded-pill">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="card border-0 shadow-lg rounded-4 mb-4">
                    <div class="card-header bg-main text-white text-center rounded-top-4 py-3">
                        <h4 class="mb-0"><i class="fas fa-receipt me-2"></i> Meus Pedidos</h4>
                    </div>

                    <div class="card-body p-4">

                        @guest
                            <form action="{{ route('meus_pedidos') }}" method="GET" class="mb-4">
                                <label for="phone" class="form-label fw-semibold">📞 Informe seu número para consultar seus
                                    pedidos:</label>
                                <div class="input-group shadow-sm">
                                    <input type="tel" name="phone" id="phone" class="form-control rounded-start-pill"
                                        value="{{ old('phone', $phone) }}" required placeholder="(00) 00000-0000">
                                    <button type="submit" class="btn btn-success rounded-end-pill px-4">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        @endguest

                        @if (isset($userNotFound) && $userNotFound)
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-user-times me-1"></i> Nenhum usuário encontrado com este telefone.
                            </div>
                        @elseif($orders !== null && count($orders) <= 0)
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-info-circle me-1"></i> Nenhum pedido encontrado.
                            </div>
                        @endif

                        @if ($orders && count($orders) > 0)
                            <div class="accordion" id="accordionOrders">
                                @foreach ($orders as $order)
                                    <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
                                        <h2 class="accordion-header" id="heading{{ $order->id }}">
                                            <button class="accordion-button collapsed bg-light rounded-0 px-4"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $order->id }}" aria-expanded="false"
                                                aria-controls="collapse{{ $order->id }}">
                                                <i class="fas fa-box-open me-2 text-main"></i>
                                                <strong>Pedido #{{ $order->id }}</strong>
                                                <span
                                                    class="ms-2 text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                                @php
                                                    $badgeClass = match ($order->status) {
                                                        'pendente' => 'bg-secondary',
                                                        'aceito' => 'bg-warning',
                                                        'em_preparo' => 'bg-info',
                                                        'pronto', 'finalizado' => 'bg-success',
                                                        default => 'bg-secondary',
                                                    };

                                                    $label = ucfirst(str_replace('_', ' ', $order->status));
                                                @endphp

                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $label }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $order->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $order->id }}" data-bs-parent="#accordionOrders">
                                            <div class="accordion-body px-4">

                                                {{-- Itens --}}
                                                <ul class="list-group list-group-flush mb-3">
                                                    @foreach ($order->items as $item)
                                                        <li class="list-group-item">
                                                            <div class="fw-bold mb-1">{{ $item['name'] }} <small
                                                                    class="text-muted">(x{{ $item['quantity'] }})</small>
                                                            </div>
                                                            @if (!empty($item['observation']))
                                                                <div class="small text-muted">📝 {{ $item['observation'] }}
                                                                </div>
                                                            @endif
                                                            @if (!empty($item['extras']))
                                                                <ul class="list-unstyled small text-muted mt-1">
                                                                    @foreach ($item['extras'] as $extra)
                                                                        <li>➕ {{ $extra['name'] }} <span
                                                                                class="text-success">(R$
                                                                                {{ number_format($extra['price'], 2, ',', '.') }})</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                {{-- Cupom e Total --}}
                                                <div
                                                    class="d-flex justify-content-between align-items-center border-top pt-3">
                                                    @if ($order->coupon)
                                                        <div>
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-ticket-alt me-1"></i>
                                                                {{ $order->coupon->code }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <span class="fw-bold me-1">Total:</span>
                                                        <span class="text-success fw-bold">R$
                                                            {{ number_format($order->total, 2, ',', '.') }}</span>
                                                    </div>
                                                </div>

                                                {{-- Endereço e Pagamento --}}
                                                <div class="mt-3 pt-3 border-top">
                                                    {{-- <@php dd($order); @endphp --}}
                                                    @if ($order->address)
                                                        <div class="mb-2">
                                                            <strong>📍 Endereço de Entrega:</strong><br>
                                                            <span
                                                                class="text-muted">{{ $order->address->formatted() }}</span>
                                                        </div>
                                                    @endif

                                                    <div class="mb-2">
                                                        <strong>💳 Forma de Pagamento:</strong><br>
                                                        <span
                                                            class="text-muted">{{ ucfirst($order->payment_method) }}</span>
                                                    </div>

                                                    @if ($order->payment_method === 'dinheiro' && !empty($order->troco))
                                                        <div>
                                                            <strong>💵 Troco para:</strong><br>
                                                            <span class="text-muted">R$
                                                                {{ number_format($order->troco, 2, ',', '.') }}</span>
                                                        </div>
                                                    @endif
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('partials.cart-modal')

    
    <script>
        window.CART_REDIRECT_URL = "{{ route('shop', $tenant->slug) }}";
        window.SABORES_PIZZA_URL = "{{ route('sabores_pizza', $tenant->slug) }}";
    </script>

    <style>
        .accordion-button {
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #eee;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .text-main {
            color: var(--main-color);
        }

        .card {
            border-radius: 20px;
        }
    </style>
@endsection
