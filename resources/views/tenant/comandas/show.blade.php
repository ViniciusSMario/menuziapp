@extends('layouts.admin')

@section('title', 'Comandas Ativas')

@section('content')
<div class="container mt-4">
    <h3>Comandas por Mesa</h3>

    <div class="row mt-3">
        @forelse ($mesas as $mesa)
            @if ($mesa->orders->count())
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $mesa->name }}</h5>
                            <p class="text-muted">{{ $mesa->orders->count() }} pedidos abertos</p>
                            <a href="{{ route('comandas.show', $mesa) }}" class="btn btn-primary btn-sm">
                                Ver Comanda
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <p class="text-muted">Nenhuma comanda ativa.</p>
        @endforelse
    </div>
</div>
@endsection
