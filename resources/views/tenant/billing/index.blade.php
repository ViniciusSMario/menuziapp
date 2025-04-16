@extends('layouts.admin')

@section('title', 'Assinatura')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="h4 fw-bold">Sua Assinatura</h1>
        <p class="text-muted">Renove sua assinatura para continuar usando o sistema.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-warning text-center">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <p class="mb-2">
                <strong>Status:</strong> 
                @if(auth()->user()->trial_ends_at && now()->lt(auth()->user()->trial_ends_at))
                    <span class="badge bg-success">Período de Teste</span>
                @else
                    <span class="badge bg-danger">Expirado</span>
                @endif
            </p>
            <p>
                <strong>Validade:</strong> 
                {{ auth()->user()->trial_ends_at ? \Carbon\Carbon::parse(auth()->user()->trial_ends_at)->format('d/m/Y') : 'Não disponível' }}
            </p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Cartão de pagamento por Pix --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Pagamento via Pix</h5>
                    <p class="text-muted">R$ 49,90 / mês</p>
                    <img src="{{ asset('images/qrcode-pix.png') }}" alt="QR Code Pix" class="img-fluid mb-3" style="max-height: 180px;">
                    <p class="small">Ou copie e cole a chave Pix abaixo:</p>
                    <input type="text" class="form-control text-center mb-3" value="chavepix@seudominio.com.br" readonly>
                    <button class="btn btn-success" disabled>Pagamento automático em breve</button>
                </div>
            </div>
        </div>

        {{-- Cartão de pagamento por link manual --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Pagamento com Cartão</h5>
                    <p class="text-muted">R$ 49,90 / mês</p>
                    <p class="small">Clique abaixo para efetuar o pagamento com cartão via plataforma segura:</p>
                    <a href="https://seulinkdepagamento.com" target="_blank" class="btn btn-primary">
                        <i class="fas fa-credit-card me-1"></i> Pagar com Cartão
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
