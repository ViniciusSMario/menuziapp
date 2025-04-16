@if ($pedidosRecentes->isEmpty())
    <div class="col-12">
        <div class="alert alert-warning text-center w-100">
            Nenhum pedido criado recentemente!
        </div>
    </div>
@else
    @foreach ($pedidosRecentes as $pedido)
        <div class="col-md-4" data-pedido-id="{{ $pedido->id }}">
            <div
                class="card shadow-sm border @if ($pedido->status == 'pendente') border-info @elseif($pedido->status == 'em_preparo') border-warning @else border-success @endif">
                <div class="card-body">
                    <h5 class="card-title">Pedido #{{ $pedido->id }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</h6>
                    <p class="mb-1"><strong>Cliente:</strong> {{ $pedido->customer_name ?? 'Cliente' }}</p>
                    <p class="mb-1"><strong>Itens:</strong></p>
                    <ul class="list-unstyled small">
                        @foreach ($pedido->items as $item)
                            <li>
                                - {{ $item['name'] }} x{{ $item['quantity'] }}
                                @if (!empty($item['observation']))
                                    <br><small class="ms-3 text-warning">📝 {{ $item['observation'] }}</small>
                                @endif
                                @if (!empty($item['extras']))
                                    <ul class="list-unstyled ms-3 text-muted">
                                        @foreach ($item['extras'] as $extra)
                                            <li>➕ {{ $extra['name'] }} (R$
                                                {{ number_format($extra['price'], 2, ',', '.') }})</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2"><strong>Total:</strong> R$ {{ number_format($pedido->total, 2, ',', '.') }}
                    </p>
                    <span
                        class="badge @if ($pedido->status == 'aceito') bg-info @elseif($pedido->status == 'em_preparo') bg-warning @elseif($pedido->status == 'finalizado') bg-success  @else bg-secondary @endif">
                        @if ($pedido->status == 'aceito')
                            Aguardando
                        @elseif($pedido->status == 'em_preparo')
                            Em preparo
                        @elseif($pedido->status == 'finalizado')
                            Concluído
                        @else
                            Pendente
                        @endif
                    </span>
                    @if ($pedido->status == 'pendente')
                        <div class="text-center">
                            <form
                                action="{{ route('tenant.pdv.aceitar', ['tenant' => $tenant, 'order' => $pedido->id]) }}"
                                method="POST" class="mt-2">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">✅ Aceitar Pedido e Enviar para a
                                    Cozinha</button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-between mt-2 p-1">
                    @if ($pedido->nota_pdf)
                        <a href="{{ asset('storage/' . $pedido->nota_pdf) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-file-pdf"></i> Ver Nota
                        </a>
                    @endif

                    <form method="POST" action="{{ route('tenant.pdv.imprimir', ['tenant' => $tenant, 'order' => $pedido->id]) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-dark me-1">
                            🖨️ Imprimir via térmica
                        </button>
                    </form>
                
                    <form method="POST"
                          action="{{ route('tenant.pdv.regenerar-nota', ['tenant' => $tenant, 'order' => $pedido->id]) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-success me-1">
                            🔄 Regerar Nota
                        </button>
                    </form>
                </div>
                
            </div>
        </div>
    @endforeach
@endif
