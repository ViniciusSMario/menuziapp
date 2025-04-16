@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">{{ isset($coupon) ? 'Editar Cupom' : 'Novo Cupom' }}</h1>
        <a href="{{ route('tenant.coupons.index', ['tenant' => $tenant->slug]) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" 
                action="{{ isset($coupon) 
                    ? route('tenant.coupons.update', ['tenant' => $tenant->slug, 'coupon' => $coupon]) 
                    : route('tenant.coupons.store', ['tenant' => $tenant->slug]) }}">

                @csrf
                @if(isset($coupon)) @method('PUT') @endif

                <div class="mb-3">
                    <label for="code" class="form-label fw-semibold">Código do Cupom</label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}"
                           class="form-control" placeholder="EXEMPLO10" required>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label fw-semibold">Tipo de Desconto</label>
                    <select name="type" class="form-select" required>
                        <option value="percent" {{ old('type', $coupon->type ?? '') == 'percent' ? 'selected' : '' }}>Porcentagem (%)</option>
                        <option value="fixed" {{ old('type', $coupon->type ?? '') == 'fixed' ? 'selected' : '' }}>Valor Fixo (R$)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="discount" class="form-label fw-semibold">Valor do Desconto</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            {{ old('type', $coupon->type ?? '') == 'percent' ? '%' : 'R$' }}
                        </span>
                        <input type="number" name="discount" step="0.01" min="0" class="form-control"
                               value="{{ old('discount', $coupon->discount ?? '') }}" placeholder="Ex: 10" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="expires_at" class="form-label fw-semibold">Data de Expiração</label>
                    <input type="date" name="expires_at" 
                           value="{{ old('expires_at', isset($coupon->expires_at) ? \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d') : '') }}" 
                           class="form-control">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Salvar Cupom
                    </button>
                    <a href="{{ route('tenant.coupons.index',['tenant' => $tenant->slug]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
