@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">
            {{ isset($neighborhood) ? 'Editar Bairro' : 'Novo Bairro' }}
        </h1>
        <a href="{{ route('tenant.neighborhoods.index', $tenant->slug) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" 
                action="{{ isset($neighborhood) 
                    ? route('tenant.neighborhoods.update', ['tenant' => $tenant->slug, 'neighborhood' => $neighborhood]) 
                    : route('tenant.neighborhoods.store', ['tenant' => $tenant->slug]) }}">

                @csrf
                @isset($neighborhood) @method('PUT') @endisset

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome do Bairro</label>
                    <input type="text" name="name" 
                        value="{{ old('name', $neighborhood->name ?? '') }}" 
                        class="form-control" placeholder="Ex: Centro" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Valor do Frete</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" step="0.01" min="0" 
                            name="shipping_cost" 
                            value="{{ old('shipping_cost', $neighborhood->shipping_cost ?? '') }}" 
                            class="form-control" placeholder="Ex: 5.00" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-save me-1"></i> Salvar Bairro
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
