@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="h3 fw-bold">Bem-vindo, {{ auth()->user()->name }} 👋</h1>
                <p class="text-muted">Admin da loja <strong>{{ $tenant->name ?? '-' }}</strong></p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Card Loja -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold text-primary"><i class="fas fa-store me-2"></i>Informações da Loja
                        </h5>
                        <p class="mb-1"><strong>Nome:</strong> {{ $tenant->name ?? '-' }}</p>
                        <p class="mb-1"><strong>Slug:</strong> <code>{{ $tenant->slug ?? '-' }}</code></p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Ativa</span></p>
                    </div>
                </div>
            </div>

            <!-- Card Ações -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold text-primary"><i class="fas fa-cogs me-2"></i>Ações Rápidas</h5>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><a href="{{ route('tenant.categories.index', tenant()->slug) }}"
                                    class="text-decoration-none"><i
                                        class="fas fa-layer-group me-2 text-secondary"></i>Gerenciar Categorias</a></li>
                            <li class="mb-2"><a href="{{ route('tenant.products.index', tenant()->slug) }}"
                                    class="text-decoration-none"><i
                                        class="fas fa-box-open me-2 text-secondary"></i>Gerenciar Produtos</a></li>
                            <li><a href="{{ route('tenant.checkout', tenant()->slug) }}" class="text-decoration-none"><i
                                        class="fas fa-credit-card me-2 text-secondary"></i>Minha Assinatura</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Card Suporte / Dicas -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold text-primary"><i class="fas fa-lightbulb me-2"></i>Dica Rápida
                        </h5>
                        <p class="text-muted">Configure os <strong>horários de funcionamento</strong> e <strong>tempo de
                                entrega</strong> nas <a
                                href="{{ route('tenant.config.edit', tenant()->slug) }}">  <strong>Configurações</strong></a> para melhorar a
                            experiência dos seus clientes.</p>
                    </div>
                </div>
            </div>

            {{-- Indicadores --}}
            <div class="row g-4 mt-0 mb-4">
                <h3 class="fw-bold">Indicadores</h3>
                <div class="col-md-2 mt-0">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted">Pedidos/Hoje</h6>
                            <h2 class="fw-bold">{{ $indicadores['pedidos_hoje'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-0">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted">Total em Vendas Hoje</h6>
                            <h2 class="fw-bold text-success">R$
                                {{ number_format($indicadores['total_hoje'] ?? 0, 2, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-0">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted">Pedidos/Mês</h6>
                            <h2 class="fw-bold">{{ $indicadores['pedidos_mes'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-0">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted">Ticket Médio Hoje</h6>
                            <h2 class="fw-bold text-info">R$
                                {{ number_format($indicadores['ticket_medio_hoje'] ?? 0, 2, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-0">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted">Produtos/Hoje</h6>
                            <h2 class="fw-bold text-secondary">{{ $indicadores['itens_hoje'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold text-primary mb-3">📊 Vendas dos últimos 7 dias</h5>
                        <div style="height: 260px;">
                            <canvas id="graficoVendas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold text-primary mb-3">📊 Status dos Pedidos</h5>
                        <div style="height: 260px;" class="d-flex justify-content-center">
                            <canvas id="graficoStatus"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">📋 Relatório de Caixas</h5>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Abertura</th>
                                <th>Fechamento</th>
                                <th>Inicial</th>
                                <th>Vendas</th>
                                <th>Pedidos</th>
                                <th>Dinheiro</th>
                                <th>Cartão</th>
                                <th>PIX</th>
                                <th>Suprimentos</th>
                                <th>Sangrias</th>
                                <th>Saldo Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registros as $r)
                                <tr>
                                    <td>{{ $r['id'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r['opened_at'])->format('d/m/Y H:i') }}</td>
                                    <td>{{ $r['closed_at'] ? \Carbon\Carbon::parse($r['closed_at'])->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td>R$ {{ number_format($r['initial_amount'], 2, ',', '.') }}</td>
                                    <td class="text-success">R$ {{ number_format($r['total_vendas'], 2, ',', '.') }}</td>
                                    <td>{{ $r['total_pedidos'] }}</td>
                                    <td>R$ {{ number_format($r['total_dinheiro'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($r['total_cartao'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($r['total_pix'], 2, ',', '.') }}</td>
                                    <td class="text-primary">R$ {{ number_format($r['suprimentos'], 2, ',', '.') }}</td>
                                    <td class="text-danger">R$ {{ number_format($r['sangrias'], 2, ',', '.') }}</td>
                                    <td class="fw-bold text-dark">R$ {{ number_format($r['saldo_final'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('graficoVendas');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($grafico['labels']),
                datasets: [{
                    label: 'Total em R$',
                    data: @json($grafico['valores']),
                    borderColor: '#057ecf',
                    backgroundColor: 'rgba(5, 126, 207, 0.2)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        new Chart(document.getElementById('graficoStatus'), {
            type: 'doughnut',
            data: {
                labels: ['Pendente', 'Aceito', 'Em Preparo', 'Finalizado'],
                datasets: [{
                    data: @json($graficoStatus['dados']),
                    backgroundColor: ['#ffc107', '#c3c3c3', '#0dcaf0', '#198754']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection
