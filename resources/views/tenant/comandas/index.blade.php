@extends('layouts.admin')

@section('title', 'Comandas Ativas')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0"><i class="fas fa-receipt me-2"></i>Comandas por Mesa</h3>
            <small class="text-muted">Última atualização: <span id="hora-atualizacao">--:--:--</span></small>

            <a href="{{ route('tenant.tables.index', ['tenant' => $tenant->slug]) }}" class="btn btn-outline-secondary">
                <i class="fas fa-table"></i> Gerenciar Mesas
            </a>
        </div>

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

        <div id="lista-comandas" class="row g-4">
            {{-- Conteúdo dinâmico via JS --}}
        </div>
    </div>

    <script>
        function carregarComandas() {
            fetch('{{ route('tenant.comandas.ativas.json', ['tenant' => $tenant->slug]) }}')
                .then(res => res.json())
                .then(mesas => {
                    let html = '';

                    mesas.forEach(mesa => {
                        if (mesa.orders.length > 0) {
                            html += `
                        <div class="col-md-3">
                            <div class="card border-primary border-2 shadow-sm h-100">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="fas fa-chair me-1"></i> ${mesa.name}
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-clock me-1"></i> Última atualização: ${new Date().toLocaleTimeString()}
                                        </p>
                                        <p class="mb-0">
                                            <span class="badge bg-warning text-dark">${mesa.orders.length} pedido(s) em aberto</span>
                                        </p>
                                    </div>
                                    <a href="/tenant/comandas/${mesa.id}" class="btn btn-outline-primary btn-sm mt-3 w-100">
                                        <i class="fas fa-eye"></i> Ver Comanda
                                    </a>
                                </div>
                            </div>
                        </div>
                        `;
                        }
                    });

                    document.getElementById('lista-comandas').innerHTML = html;
                    this.atualizarHora();
                })
                .catch(error => {
                    console.error('Erro ao carregar comandas:', error);
                });
        }

        function atualizarHora() {
            const agora = new Date();
            const hora = agora.toLocaleTimeString('pt-BR');
            document.getElementById('hora-atualizacao').innerText = hora;
        }
        carregarComandas();
        setInterval(carregarComandas, 30000);
    </script>
@endsection
