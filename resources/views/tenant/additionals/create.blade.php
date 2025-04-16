@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">Novo Adicional</h1>
        <a href="{{ route('tenant.additionals.index', $tenant->slug) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('tenant.additionals.store', ['tenant' => $tenant->slug]) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome do Adicional</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Queijo extra" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Preço</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="Ex: 2.50" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Categoria</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Salvar Adicional
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
