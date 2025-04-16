@extends('layouts.admin')

@section('title', isset($table) ? 'Editar Mesa' : 'Nova Mesa')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">
            {{ isset($table) ? 'Editar Mesa' : 'Nova Mesa' }}
        </h1>
        <a href="{{ route('tenant.tables.index', $tenant->slug) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form 
                action="{{ isset($table) 
                    ? route('tenant.tables.update', ['tenant' => $tenant->slug, 'table' => $table]) 
                    : route('tenant.tables.store', ['tenant' => $tenant->slug]) }}"
                method="POST">

                @csrf
                @if (isset($table))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nome da Mesa</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $table->name ?? '') }}"
                           class="form-control" placeholder="Ex: Mesa 01" required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-save me-1"></i>
                    {{ isset($table) ? 'Atualizar Mesa' : 'Cadastrar Mesa' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
