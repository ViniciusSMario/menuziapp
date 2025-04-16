<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 0;
            padding: 5px;
            font-family: monospace;
            font-size: 11px;
            line-height: 1.3;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .mt {
            margin-top: 10px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        hr.cut-line {
            border: none;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }


        .total-line {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>

    <div class="center bold">Pedido #{{ $order->id }}</div>
    <div class="center">Entrega Própria</div>
    <div class="divider"></div>

    <div><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
    <div><strong>Cliente:</strong> {{ $order->customer_name }}</div>
    <div><strong>Telefone:</strong> {{ $order->customer_phone }}</div>
    @if ($order->delivery_type == 'delivery')
        <div><strong>Endereço:</strong> {{ $order->address->rua }}, {{ $order->address->numero }},
        {{ $order->address->bairro }} - {{ $order->address->cidade }}/{{ $order->address->estado }}</div>
    @endif

    <div class="divider"></div>
    <div class="bold">ITENS DO PEDIDO</div>

    @foreach ($order->items as $item)
        <div>
            {{ $item['quantity'] }}x {{ $item['name'] }}
            @if (!empty($item['extras']))
                <br><small>+
                    @foreach ($item['extras'] as $extra)
                        {{ $extra['name'] }} (R$ {{ number_format($extra['price'], 2, ',', '.') }})@if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                </small>
            @endif
            @if (!empty($item['observation']))
                <br><small>📝 {{ $item['observation'] }}</small>
            @endif
        </div>
        <div class="total-line">
            <span>Subtotal:</span>
            <span>
                R$
                {{ number_format(($item['price'] + collect($item['extras'] ?? [])->sum('price')) * $item['quantity'], 2, ',', '.') }}
            </span>
        </div>
        <div class="divider"></div>
    @endforeach

    @if (!empty($order->delivery_type == 'delivery'))
        <div class="total-line"><span>Taxa de Entrega:</span> <span>R$
                {{ number_format($order->shipping_cost, 2, ',', '.') }}</span></div>
    @endif

    <div class="divider"></div>
    <div class="total-line bold">
        <span>Total:</span>
        <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
    </div>

    @if (!empty($order->payment_method))
        <div class="mt"><strong>Pagamento:</strong> {{ Str::ucfirst($order->payment_method) }}</div>
    @endif

    @if (!empty($order->payment_method == 'dinheiro'))
        <div class="mt"><strong>Troco Para:</strong>
            <span>R$ {{ number_format($order->troco, 2, ',', '.') }}</span>
        </div>
    @endif
    <hr class="cut-line">
</body>

</html>
