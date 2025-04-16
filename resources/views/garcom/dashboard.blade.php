@extends('layouts.garcom')

@section('title', 'Garçom - Mesas')

@section('content')
<div class="container py-3">
    <h4 class="text-center mb-4 fw-bold">Mesas com Comandas</h4>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <div class="row g-3">
        @forelse ($mesas as $mesa)
            <div class="col-6 col-sm-4 col-md-3">
                <a href="{{ route('garcom.mesa', $mesa) }}" class="text-decoration-none">
                    <div class="card shadow-sm text-center p-2 h-100 mesa-card 
                        {{ $mesa->orders->count() > 0 ? 'bg-light border-danger' : 'bg-white' }}">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="fas fa-chair fa-2x {{ $mesa->orders->count() > 0 ? 'text-danger' : 'text-success' }} mb-2"></i>
                            <h6 class="fw-bold">{{ $mesa->name }}</h6>

                            <span class="badge {{ $mesa->orders->count() > 0 ? 'bg-danger' : 'bg-success' }}">
                                {{ $mesa->orders->count() > 0 ? 'Ocupada' : 'Disponível' }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">Nenhuma mesa encontrada.</div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .mesa-card {
        transition: transform 0.2s ease-in-out;
        border-radius: 12px;
    }

    .mesa-card:hover {
        transform: scale(1.04);
    }

    @media (max-width: 576px) {
        .mesa-card h6 {
            font-size: 1rem;
        }

        .mesa-card i {
            font-size: 1.5rem;
        }
    }
</style>
@endsection
