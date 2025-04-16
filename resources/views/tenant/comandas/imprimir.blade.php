<!DOCTYPE html>
<html>
<head>
    <title>Comanda - {{ $table->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; padding: 20px; }
        h2 { margin-bottom: 10px; }
        ul { padding-left: 15px; }
        .total { font-weight: bold; margin-top: 10px; }
        .item { margin-bottom: 5px; }
    </style>
</head>
<body onload="window.print()">
    <h2>Comanda: {{ $table->name }}</h2>
    <hr>

    @foreach ($orders as $order)
        <p><strong>Pedido #{{ $order->id }}</strong> - {{ $order->customer_name }}</p>
        <ul>
            @foreach ($order->items as $item)
                <li class="item">{{ $item['name'] }} x{{ $item['qty'] }}</li>
            @endforeach
        </ul>
        <p class="total">Total: R$ {{ number_format($order->total, 2, ',', '.') }}</p>
        <hr>
    @endforeach

    <p style="text-align: center; margin-top: 30px;">Obrigado!</p>
</body>
</html>