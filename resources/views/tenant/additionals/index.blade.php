@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h4 fw-bold">Adicionais</h1>
            <div class="d-flex flex-wrap gap-2">

                <a href="{{ route('tenant.additionals.create', ['tenant' => tenant()->slug]) }}"
                    class="btn btn-primary rounded-pill">
                    <i class="fas fa-plus-circle me-1"></i> Novo Adicional
                </a>

                <a href="{{ route('tenant.additionals.import.form', tenant()->slug) }}" class="btn btn-success rounded-pill">
                    <i class="fas fa-file-import me-1"></i> Importar
                </a>

                <button id="btnDownloadModelo" class="btn btn-outline-success rounded-pill">
                    <i class="fas fa-download me-1"></i> Baixar modelo de importação
                </button>
            </div>
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
            <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">{{ session('success') }}</div>
        @endif

        {{-- Tabela de adicionais --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Categoria</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($additionals as $additional)
                                <tr>
                                    <td>{{ $additional->name }}</td>
                                    <td>R$ {{ number_format($additional->price, 2, ',', '.') }}</td>
                                    <td>{{ $additional->category->name ?? '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.additionals.edit', ['tenant' => tenant()->slug, 'additional' => $additional->id]) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST"
                                            action="{{ route('tenant.additionals.destroy', ['tenant' => tenant()->slug, 'additional' => $additional->id]) }}"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Nenhum adicional cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
