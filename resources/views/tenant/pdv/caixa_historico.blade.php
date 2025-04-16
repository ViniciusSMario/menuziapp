@extends('layouts.admin')

@section('title', 'Histórico de Caixas')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Histórico de Caixas</h3>

    <form method="GET" class="row mb-4">
        <div class="col-md-4">
            <label>Data Início:</label>
            <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
        </div>
        <div class="col-md-4">
            <label>Data Fim:</label>
            <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary me-2">Filtrar</button>
            <a href="{{ route('caixa.historico') }}" class="btn btn-secondary">Limpar</a>
        </div>
    </form>

    <div class="table-responsive shadow-sm">
        <table class="table table-bordered table-hover align-middle bg-white">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Aberto por</th>
                    <th>Abertura</th>
                    <th>Fechamento</th>
                    <th>Valor Inicial</th>
                    <th>Valor Final</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($caixas as $caixa)
                    <tr>
                        <td>#{{ $caixa->id }}</td>
                        <td>{{ $caixa->user->name }}</td>
                        <td>{{ $caixa->opened_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $caixa->closed_at?->format('d/m/Y H:i') }}</td>
                        <td>R$ {{ number_format($caixa->initial_amount, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($caixa->final_amount, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('caixa.relatorio', $caixa->id) }}" class="btn btn-sm btn-outline-primary">
                                Ver Relatório
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Nenhum caixa encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $caixas->links() }}
    </div>
</div>
@endsection
