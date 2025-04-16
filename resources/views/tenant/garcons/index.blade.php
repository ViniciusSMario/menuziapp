@extends('layouts.admin')

@section('title', 'Garçons')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold">Garçons</h1>
        <a href="{{ route('tenant.garcons.create', ['tenant' => tenant()->slug]) }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-user-plus me-1"></i> Novo Garçom
        </a>
    </div>

    {{-- Mensagens de feedback --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela de garçons --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($garcons as $garcom)
                            <tr>
                                <td>{{ $garcom->name }}</td>
                                <td>{{ $garcom->email }}</td>
                                <td class="text-end">
                                    <form action="{{ route('tenant.garcons.destroy', ['tenant' => tenant()->slug, 'id' => $garcom->id]) }}"
                                          method="POST"
                                          class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i> Remover
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Nenhum garçom cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
