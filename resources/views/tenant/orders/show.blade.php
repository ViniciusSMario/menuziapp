@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="card shadow">
        <div class="card-body">
            <h1 class="h5 fw-bold mb-3">Pedido #{{ $order->id }}</h1>
            <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
            <p><strong>Telefone:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Status:</strong> <span class="badge bg-info text-dark">{{ ucfirst($order->status) }}</span></p>

            <h5 class="mt-4">Itens:</h5>
            <ul class="list-group mb-3">
                @foreach ($order->items as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $item['name'] }} (x{{ $item['qty'] }})
                        <span>R$ {{ number_format($item['price'], 2, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            <p class="fw-bold">Total: R$ {{ number_format($order->total, 2, ',', '.') }}</p>

            <a href="{{ route('tenant.orders.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection
