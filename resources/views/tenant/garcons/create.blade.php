@extends('layouts.admin')

@section('title', 'Novo Garçom')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">Cadastrar Garçom</h1>
        <a href="{{ route('tenant.garcons.index', $tenant->slug) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.garcons.store', ['tenant' => $tenant->slug]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nome</label>
                    <input type="text" name="name" class="form-control" placeholder="Nome completo do garçom" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-mail</label>
                    <input type="email" name="email" class="form-control" placeholder="email@exemplo.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Senha</label>
                    <input type="password" name="password" class="form-control" placeholder="Defina uma senha" required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-user-plus me-1"></i> Cadastrar Garçom
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
