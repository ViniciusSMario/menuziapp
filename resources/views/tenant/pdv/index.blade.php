@extends('layouts.admin')

@section('title', 'PDV')

@section('content')
    <div class="py-0">
        {{-- MENSAGENS --}}
        <div class="row mb-4">
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
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>

        {{-- STATUS DO CAIXA --}}
        @if (!$caixaAtual)
            <div class="text-center mb-4">
                <button class="btn btn-lg btn-success" data-toggle="modal" data-target="#modalAbrirCaixa">
                    <i class="fas fa-cash-register me-1"></i> Abrir Caixa
                </button>
            </div>
        @else
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="alert alert-success shadow-sm rounded-3">
                        <strong>💵 Caixa em operação</strong><br>
                        Operador: <strong>{{ $caixaAtual->user->name }}</strong><br>
                        Abertura: {{ $caixaAtual->opened_at->format('d/m/Y H:i') }}<br>
                        <span class="badge bg-success mt-2">Saldo Atual: R$
                            {{ number_format($caixaAtual->initial_amount + $caixaAtual->orders->sum('total') + $caixaAtual->movements->where('type', 'suprimento')->sum('amount') - $caixaAtual->movements->where('type', 'sangria')->sum('amount'), 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-6 d-flex flex-column gap-2">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-warning w-100 mb-2" data-toggle="modal" data-target="#modalSuprimento">
                                <i class="fas fa-plus-circle me-1"></i> Suprimento
                            </button>
                            <button class="btn btn-danger w-100" data-toggle="modal" data-target="#modalSangria">
                                <i class="fas fa-minus-circle me-1"></i> Sangria
                            </button>
                        </div>
                        <div class="col-md-6">
                            <form id="formFecharCaixa"
                                action="{{ route('tenant.caixa.fechar', ['tenant' => $tenant->slug]) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-secondary w-100 mb-2"
                                    onclick="confirmarFechamentoCaixa()">
                                    <i class="fas fa-lock me-1"></i> Fechar Caixa
                                </button>
                            </form>

                            <a href="{{ route('tenant.pdv.touch', ['tenant' => $tenant->slug]) }}"
                                class="btn btn-success w-100">
                                <i class="fas fa-plus me-1"></i> Novo Pedido
                            </a>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('tenant.cozinha.index', ['tenant' => $tenant->slug]) }}" target="_blank"
                                class="btn btn-info w-100">
                                <i class="fas fa-desktop me-1"></i> Monitor Cozinha
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold mb-0">🕒 Pedidos Recentes</h5>
            <small class="text-muted" id="ultima-atualizacao">Última atualização: --</small>
        </div>
        
        @if ($caixaAtual)
            <div id="loading-pedidos" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2 text-muted">Carregando pedidos recentes...</p>
            </div>

            <div id="pedidos-recentes-container" class="row g-4 d-none">
                @include('tenant.pdv._pedidos_recentes', [
                    'tenant' => $tenant->slug,
                    'pedidosRecentes' => $pedidosRecentes,
                ])
            </div>
        @else
            <div class="alert alert-warning text-center">
                Nenhum caixa está aberto no momento. Abra um caixa para visualizar os pedidos.
            </div>
        @endif
    
        <audio id="notificacao-audio" src="{{ asset('sounds/notification.wav') }}" preload="auto"></audio>

        <!-- Modal Suprimento -->
        <div class="modal fade" id="modalSuprimento" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('tenant.caixa.suprimento', ['tenant' => $tenant->slug]) }}"
                    class="modal-content">
                    @csrf
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">Registrar Suprimento</h5>
                        <button type="button" class="btn-close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor:</label>
                            <input type="number" name="amount" step="0.01" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição:</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Sangria -->
        <div class="modal fade" id="modalSangria" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('tenant.caixa.sangria', ['tenant' => $tenant->slug]) }}"
                    class="modal-content">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Registrar Sangria</h5>
                        <button type="button" class="btn-close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor:</label>
                            <input type="number" name="amount" step="0.01" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição:</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Abrir Caixa -->
        <div class="modal fade" id="modalAbrirCaixa" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('tenant.caixa.abrir', ['tenant' => $tenant->slug]) }}"
                    class="modal-content">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Abrir Caixa</h5>
                        <button type="button" class="btn-close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $erro)
                                        <li>{{ $erro }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="initial_amount" class="form-label">Valor Inicial:</label>
                            <input type="number" name="initial_amount" step="0.01" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Confirmar Abertura</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const audio = document.getElementById('notificacao-audio');
        let audioLiberado = false;
        let pedidosAnteriores = [];

        function tocarNotificacao() {
            if (audioLiberado) {
                audio.play().catch(err => console.warn("Erro ao tocar áudio:", err));
            }
        }

        function carregarPedidosRecentes() {
            const pedidosRoute = `pdv/pedidos-recentes`;

            // Exibe loading e esconde os pedidos
            document.getElementById('loading-pedidos').classList.remove('d-none');
            document.getElementById('pedidos-recentes-container').classList.add('d-none');

            fetch(pedidosRoute)
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    const novosPedidos = Array.from(tempDiv.querySelectorAll('[data-pedido-id]')).map(el => el.dataset
                        .pedidoId);

                    const houveNovo = novosPedidos.some(id => !pedidosAnteriores.includes(id));

                    document.getElementById('loading-pedidos').classList.add('d-none');
                    const container = document.getElementById('pedidos-recentes-container');
                    container.classList.remove('d-none');
                    container.innerHTML = html;

                    document.getElementById('pedidos-recentes-container').innerHTML = html;

                    const agora = new Date();
                    const formatado = agora.toLocaleString('pt-BR');
                    document.getElementById('ultima-atualizacao').innerText = `Última atualização: ${formatado}`;

                    pedidosAnteriores = novosPedidos;

                    if (houveNovo) {
                        tocarNotificacao();
                    }
                })
                .catch(error => console.error('Erro ao carregar pedidos recentes:', error));
        }

        function tentarLiberarAudio() {
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                audioLiberado = true;
                console.log("🔓 Áudio liberado!");
            }).catch((e) => {
                console.warn("🔒 Navegador bloqueou o áudio. Clique necessário.", e);
            });
        }

        document.addEventListener('click', () => {
            if (!audioLiberado) {
                tentarLiberarAudio();
            }
        }, {
            once: true
        });

        @if ($caixaAtual)
            carregarPedidosRecentes();
            setInterval(carregarPedidosRecentes, 20000);
        @endif

        function confirmarFechamentoCaixa() {
            Swal.fire({
                title: 'Deseja realmente fechar o caixa?',
                text: "Essa ação encerrará as operações do dia.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, fechar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formFecharCaixa').submit();
                }
            });
        }
    </script>

@endsection
