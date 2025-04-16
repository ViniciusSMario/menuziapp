<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 5px;
            font-family: monospace;
            font-size: 11px;
            line-height: 1.3;
        }

        .center {
            text-align: center;
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="center">
        PEDIDO #{{ $order->id }}<br>
        RapiDelivery<br>
        {{ $order->created_at->format('d/m/Y H:i') }}
    </div>

    <div class="line"></div>
    <strong>Cliente:</strong> {{ $order->customer_name }}<br>
    <strong>Tel:</strong> {{ $order->customer_phone }}<br>
    @if (!empty($order->delivery_type == 'delivery'))
        <strong>Endereço:</strong>{{ $order->address->rua }}, {{ $order->address->numero }},
        {{ $order->address->bairro }} - {{ $order->address->cidade }}/{{ $order->address->estado }}
        <br>
    @endif
    <div class="line"></div>
    <div class="bold">ITENS DO PEDIDO</div>

    @foreach ($order->items as $item)
        {{ $item['quantity'] }}x {{ $item['name'] }}<br>

        @if (!empty($item['extras']))
            @foreach ($item['extras'] as $extra)
                &nbsp;&nbsp;+ {{ $extra['name'] }} (R$ {{ number_format($extra['price'], 2, ',', '.') }})<br>
            @endforeach
        @endif

        @if (!empty($item['observation']))
            📝 {{ $item['observation'] }}<br>
        @endif
        <br>
    @endforeach

    <div class="line"></div>
    @if (!empty($order->delivery_type == 'delivery'))
        <div class="total-line"><span><strong>Taxa de Entrega:</strong></span> <span>R$
                {{ number_format($order->shipping_cost, 2, ',', '.') }}</span></div>
    @endif

    <div class="divider"></div>
    <div class="total-line bold">
        <span><strong>Total:</strong></span>
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
    <div class="line"></div>
    <div class="center">Obrigado pela preferência!</div>
</body>

</html>
