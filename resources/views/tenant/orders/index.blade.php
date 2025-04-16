@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h1 class="h4 fw-bold mb-4">Pedidos</h1>

    @foreach ($orders as $order)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Cliente: {{ $order->customer_name }} - {{ $order->customer_phone }}</h5>
                <p class="card-text">Total: R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                <p class="card-text">Status: <span class="badge bg-info text-dark">{{ ucfirst($order->status) }}</span></p>

                <div class="d-flex gap-2">
                    <a href="{{ route('tenant.orders.show', $order) }}" class="btn btn-primary btn-sm">Ver detalhes</a>

                    <form method="POST" action="{{ route('tenant.orders.update', $order) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-warning btn-sm">Avançar Status</button>
                    </form>

                    <form method="POST" action="{{ route('tenant.orders.destroy', $order) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
