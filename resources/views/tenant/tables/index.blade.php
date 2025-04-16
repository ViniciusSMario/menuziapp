@extends('layouts.admin')

@section('title', 'Mesas')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold">Mesas</h1>
        <a href="{{ route('tenant.tables.create', ['tenant' => tenant()->slug]) }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus-circle me-1"></i> Nova Mesa
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tables as $table)
                            <tr>
                                <td>{{ $table->name }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.tables.edit', ['tenant' => tenant()->slug, 'table' => $table->id]) }}"
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>

                                    <form action="{{ route('tenant.tables.destroy', ['tenant' => tenant()->slug, 'table' => $table->id]) }}"
                                          method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i> Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Nenhuma mesa cadastrada ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
