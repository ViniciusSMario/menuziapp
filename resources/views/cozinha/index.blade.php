@extends('layouts.kitchen')

@section('title', 'Monitor de Cozinha')

@section('content')
    <div class="container-fluid py-3">
        <h3 class="mb-4 fw-bold">🍳 Monitor de Cozinha</h3>
        <div>
            <h5>Filtros</h5>
        </div>
        <div id="filtros" class="mb-3 d-flex flex-wrap gap-2 align-items-center">
            <span class="badge bg-secondary filtro-tempo border py-2 px-3 shadow-sm" data-filtro="no_prazo" role="button">No
                prazo</span>
            <span class="badge bg-warning text-dark filtro-tempo border py-2 px-3 shadow-sm" data-filtro="atrasado"
                role="button">Atrasado</span>
            <span class="badge bg-danger filtro-tempo border py-2 px-3 shadow-sm" data-filtro="muito_atrasado"
                role="button">Muito atrasado</span>

            <div class="ml-5 d-flex justify-content-center gap-2">
                <span class="badge text-bg-light border border-secondary text-dark py-2 px-3 shadow-sm"
                    id="count-pendentes">
                    🕐 Pendentes: <strong>0</strong>
                </span>
                <span class="badge text-bg-light border border-warning text-dark py-2 px-3 shadow-sm" id="count-preparo">
                    🔥 Em Preparo: <strong>0</strong>
                </span>
                <span class="badge text-bg-light border border-success text-dark py-2 px-3 shadow-sm"
                    id="count-finalizados">
                    ✅ Finalizados: <strong>0</strong>
                </span>
            </div>

            <div class="ms-auto text-muted">
                Última atualização: <span id="hora-atualizacao">--:--:--</span>
            </div>
        </div>

        @if (!$caixaAberto)
            <div class="alert alert-warning text-center">
                Nenhum caixa está aberto no momento.
            </div>
        @else
            <div class="row g-4" id="lista-comandas">
                {{-- Conteúdo dinâmico via JS --}}
            </div>
            <div class="row g-4" id="comandas-finalizadas">
                {{-- Comandas finalizadas --}}
            </div>
        @endif
    </div>

    <audio id="notificacao-audio" src="{{ asset('sounds/notification.wav') }}" preload="auto"></audio>

    <script>
        const atualizarStatusUrl = "{{ route('tenant.cozinha.status', ['tenant' => tenant()->slug,'order' => '__ID__']) }}";
        let filtroTempoSelecionado = null;

        document.querySelectorAll('.filtro-tempo').forEach(btn => {
            btn.addEventListener('click', function() {
                if (filtroTempoSelecionado === this.dataset.filtro) {
                    filtroTempoSelecionado = null;
                    document.querySelectorAll('.filtro-tempo').forEach(el => el.classList.remove('border',
                        'border-dark'));
                } else {
                    filtroTempoSelecionado = this.dataset.filtro;
                    document.querySelectorAll('.filtro-tempo').forEach(el => el.classList.remove('border',
                        'border-dark'));
                    this.classList.add('border', 'border-dark');
                }
                carregarComandas();
            });
        });

        function atualizarHora() {
            const agora = new Date();
            document.getElementById('hora-atualizacao').innerText = agora.toLocaleTimeString('pt-BR');
        }

        const audio = document.getElementById('notificacao-audio');
        let audioLiberado = false;

        function tentarLiberarAudio() {
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                audioLiberado = true;
                console.log("🔓 Áudio liberado automaticamente!");
            }).catch((e) => {
                console.warn("🔒 Navegador bloqueou o áudio. Será necessário um clique do usuário.", e);
            });
        }

        // Tenta liberar o áudio automaticamente após carregamento
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                // Simula um "clique" virtual (em alguns casos funciona)
                const evt = new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true
                });
                document.body.dispatchEvent(evt);
            }, 500); // espera 0.5s antes de tentar
        });

        // Listener real de clique — fallback garantido
        document.addEventListener('click', () => {
            if (!audioLiberado) {
                tentarLiberarAudio();
            }
        }, {
            once: true
        });

        let pedidosAnteriores = [];

        function carregarComandas() {
            fetch("{{ route('tenant.cozinha.json', ['tenant' => tenant()->slug]) }}")
                .then(res => res.json())
                .then(data => {
                    const pendentes = data.filter(p => p.status === 'aceito');

                    // Verifica se há novos pedidos pendentes
                    const novos = pendentes.filter(p => !pedidosAnteriores.some(antigo => antigo.id === p.id));
                    
                    if (novos.length > 0) {
                        document.getElementById('notificacao-audio').play();
                        toastr.success(`🔔 ${novos.length} novo(s) pedido(s) recebido(s)!`);
                    }

                    pedidosAnteriores = pendentes;

                    const emPreparo = data.filter(p => p.status === 'em_preparo');
                    const finalizados = data.filter(p => p.status === 'finalizado');

                    document.getElementById('count-pendentes').innerText = `Pendentes: ${pendentes.length}`;
                    document.getElementById('count-preparo').innerText = `Em Preparo: ${emPreparo.length}`;
                    document.getElementById('count-finalizados').innerText = `Finalizados: ${finalizados.length}`;

                    let html = `
                        <div class="col-md-12">
                            <h4 class="fw-bold mb-3 border-bottom pb-2 mt-2 text-warning">
                                Pedidos Abertos
                            </h4>
                            <div class="p-3 border rounded bg-light shadow-sm">
                                <div class="row g-3">
                                    ${renderCards(pendentes)}
                                </div>
                           
                                <div class="row g-3 mt-1">
                                    ${renderCards(emPreparo)}
                                </div>
                            </div>
                        </div>
                    `;

                    let finalizadosHtml = '';
                    if (finalizados.length > 0) {
                        finalizadosHtml += `<div class="col-12">
                            <h4 class="fw-bold mt-5 mb-3 border-bottom pb-2 text-success">
                                ✅ Pedidos Finalizados <span class="badge bg-success ms-1">${finalizados.length}</span>
                            </h4>
                            <div class="p-3 border rounded bg-light shadow-sm">
                                <div class="row g-3">
                                    ${renderCards(finalizados)}
                                </div>
                            </div>
                        </div>`;
                    }

                    document.getElementById('lista-comandas').innerHTML = html;
                    document.getElementById('comandas-finalizadas').innerHTML = finalizadosHtml;
                    atualizarHora();
                });
        }

        function renderCards(pedidos) {
            return pedidos.map(pedido => {
                const tempo = calcularTempo(pedido.created_at);
                const tempoMin = tempoMinutos(tempo);

                var statusClass = tempoMinutos(tempo) < 600 ? 'border-secondary' : tempoMinutos(tempo) < 20 ?
                    'border-warning' : 'border-danger';

                if (pedido.status == 'finalizado') {
                    statusClass = 'border-success';
                };

                let filtroAplica = true;
                if (filtroTempoSelecionado === 'no_prazo' && tempoMin >= 10) filtroAplica = false;
                if (filtroTempoSelecionado === 'atrasado' && (tempoMin < 10 || tempoMin >= 20)) filtroAplica =
                    false;
                if (filtroTempoSelecionado === 'muito_atrasado' && tempoMin < 20) filtroAplica = false;

                if (!filtroAplica) return '';

                return `
                    <div class="col-12 col-md-4">
                        <div class="card ${statusClass} border-1 shadow-sm">
                            <div class="card-header d-flex justify-content-between bg-${statusCor(pedido.status)}">
                                <span><strong>${pedido.cliente ?? 'Cliente'}</strong></span>
                                <span class=" text-white">${tempo}</span>
                            </div>
                            <div class="card-body">
                                <p class="mb-1 text-muted">Comanda #${pedido.id}</p>
                                <ul class="list-group">
                                    ${pedido.itens.map(item => `
                                                        <li class="list-group-item">
                                                            <div class="fw-semibold">${item.quantity}x ${item.name}</div>

                                                            ${item.observation && item.observation.trim() !== '' ? `
                                                <div class="text-muted small mt-1">📝 ${item.observation}</div>
                                            ` : ''}
                                                            ${item.extras && item.extras.length > 0 ? `
                                                <ul class="list-unstyled mt-1 mb-0 small text-muted">
                                                    ${item.extras.map(extra => `
                                                                        <li>➕ ${extra.name} (R$ ${parseFloat(extra.price).toFixed(2).replace('.', ',')})</li>
                                                                    `).join('')}
                                                </ul>
                                            ` : ''}
                                                        </li>
                                                    `).join('')}
                                </ul>

                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <span class="badge bg-${statusCor(pedido.status)}">${pedido.status.replace('_', ' ').toUpperCase()}</span>
                                    ${pedido.status !== 'finalizado' ? `
                                                                <button class="btn btn-sm btn-outline-primary" onclick="atualizarStatus(${pedido.id}, '${pedido.status}')">
                                                                    <i class="fas fa-sync-alt me-1"></i> Atualizar Status
                                                                </button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>`;
            }).join('');
        }

        function tempoMinutos(tempoString) {
            const partes = tempoString.split(":");
            return parseInt(partes[0]) * 60 + parseInt(partes[1]);
        }

        function calcularTempo(createdAt) {
            const inicio = new Date(createdAt);
            const agora = new Date();
            const diff = Math.floor((agora.getTime() - inicio.getTime()) / 1000);

            const horas = Math.floor(diff / 3600);
            const minutos = Math.floor((diff % 3600) / 60);
            const segundos = diff % 60;

            const h = horas > 0 ? String(horas).padStart(2, '0') + ':' : '';
            const m = String(minutos).padStart(2, '0');
            const s = String(segundos).padStart(2, '0');

            return `${h}${m}:${s}`;
        }

        function statusCor(status) {
            switch (status) {
                case 'aceito':
                    return 'secondary-custom';
                case 'em_preparo':
                    return 'warning';
                case 'finalizado':
                    return 'success';
            }
        }

        function atualizarStatus(orderId, statusAtual) {
            let proximoStatus = null;

            if (statusAtual === 'aceito') proximoStatus = 'em_preparo';
            else if (statusAtual === 'em_preparo') proximoStatus = 'finalizado';
            else return;

            const url = atualizarStatusUrl.replace('__ID__', orderId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status: proximoStatus
                })
            }).then(() => carregarComandas());
        }

        carregarComandas();
        setInterval(carregarComandas, 30000);
    </script>
    <style>
        .bg-secondary-custom {
            background-color: #d1d1d1;
        }
    </style>
@endsection
