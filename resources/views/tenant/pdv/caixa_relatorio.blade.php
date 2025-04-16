@extends('layouts.admin')

@section('title', 'Relatório de Caixa')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Relatório do Caixa #{{ $caixa->id }}</h4>
        <div>
            <a href="{{ route('caixa.export.pdf', $caixa->id) }}" class="btn btn-outline-danger">Exportar PDF</a>
            <a href="{{ route('caixa.export.excel', $caixa->id) }}" class="btn btn-outline-success">Exportar Excel</a>
        </div>
    </div>

    <div class="card p-4 shadow mb-4">
        <h5 class="text-primary">Informações Gerais</h5>
        <p><strong>Aberto por:</strong> {{ $caixa->user->name }}</p>
        <p><strong>Data de Abertura:</strong> {{ $caixa->opened_at->format('d/m/Y H:i') }}</p>
        <p><strong>Data de Fechamento:</strong> {{ $caixa->closed_at?->format('d/m/Y H:i') ?? '—' }}</p>
        <p><strong>Valor Inicial:</strong> R$ {{ number_format($caixa->initial_amount, 2, ',', '.') }}</p>
    </div>

    <div class="card p-4 shadow mb-4">
        <h5 class="text-success">Totais em Vendas</h5>
        <p><strong>Total de Pedidos:</strong> {{ $caixa->orders->count() }}</p>
        <p><strong>Valor Total:</strong> R$ {{ number_format($caixa->orders->sum('total'), 2, ',', '.') }}</p>

        @php
            $porPagamento = $caixa->orders->groupBy('payment_method');
        @endphp

        <ul class="list-group">
            @foreach ($porPagamento as $metodo => $pedidos)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ ucfirst($metodo) }}</span>
                    <span>R$ {{ number_format($pedidos->sum('total'), 2, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card p-4 shadow mb-4">
        <h5 class="text-warning">Movimentações</h5>
        <p><strong>Suprimentos:</strong> R$ {{ number_format($caixa->movements->where('type', 'suprimento')->sum('amount'), 2, ',', '.') }}</p>
        <p><strong>Sangrias:</strong> R$ {{ number_format($caixa->movements->where('type', 'sangria')->sum('amount'), 2, ',', '.') }}</p>
    </div>

    <div class="card p-4 shadow">
        <h5 class="text-dark">Resumo Final</h5>

        @php
            $totalVendas = $caixa->orders->sum('total');
            $suprimentos = $caixa->movements->where('type', 'suprimento')->sum('amount');
            $sangrias = $caixa->movements->where('type', 'sangria')->sum('amount');
            $final = $caixa->initial_amount + $totalVendas + $suprimentos - $sangrias;
        @endphp

        <p><strong>Saldo Final Esperado:</strong> R$ {{ number_format($final, 2, ',', '.') }}</p>
    </div>
</div>
@endsection
