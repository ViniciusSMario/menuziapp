@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">{{ isset($category) ? 'Editar Categoria' : 'Nova Categoria' }}</h1>
        <a href="{{ route('tenant.categories.index', tenant()->slug) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" 
                action="{{ isset($category) 
                    ? route('tenant.categories.update', ['tenant' => tenant()->slug, 'category' => $category->id]) 
                    : route('tenant.categories.store', tenant()->slug) 
                }}">
                
                @csrf
                @isset($category) @method('PUT') @endisset

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome da Categoria</label>
                    <input type="text" name="name" 
                           value="{{ old('name', $category->name ?? '') }}" 
                           class="form-control" placeholder="Ex: Pizzas, Bebidas, Sobremesas..." required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-save me-1"></i> Salvar Categoria
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
