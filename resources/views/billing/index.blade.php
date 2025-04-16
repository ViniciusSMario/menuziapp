@extends('layouts.admin')

@section('title', 'Assinar Plano')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-store fa-3x text-primary mb-3"></i>
            <h1 class="fw-bold mb-2">Ative sua Loja</h1>
            <p class="text-muted">Aproveite todos os recursos do sistema com nosso plano completo.</p>
        </div>

        <div class="card shadow-sm p-4 mx-auto" style="max-width: 400px;">
            <h4 class="mb-3 fw-bold text-success">Plano Mensal</h4>
            <p class="fs-5 text-dark mb-2">R$ 49,90 / mês</p>
            <p class="text-muted small">Cobrança recorrente • Cancele quando quiser</p>

            <a href="{{ route('tenant.billing.checkout', ['tenant' => tenant()->slug]) }}" class="btn btn-success btn-lg w-100 mt-3">
                <i class="fas fa-credit-card me-1"></i> Assinar Agora
            </a>
            <small class="text-muted d-block mt-3">{{ route('tenant.billing.checkout', ['tenant' => tenant()->slug]) }}</small>

        </div>

        @if(auth()->user()->trial_ends_at && now()->lt(auth()->user()->trial_ends_at))
            <p class="text-info mt-4">
                Você está em período de teste até <strong>{{ \Carbon\Carbon::parse(auth()->user()->trial_ends_at)->format('d/m/Y') }}</strong>
            </p>
        @endif
    </div>
</div>
@endsection
