<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Comanda Pedido #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .comanda { width: 300px; }
        .comanda h2 { text-align: center; }
        .item { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; margin-top: 20px; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>
    <div class="comanda">
        <h2>Comanda #{{ $order->id }}</h2>
        <p>Cliente: {{ $order->customer_name }}</p>
        <p>Método: {{ ucfirst($order->payment_method) }}</p>
        @if($order->payment_method === 'dinheiro' && $order->troco)
            <p>Troco para: R$ {{ number_format($order->troco, 2, ',', '.') }}</p>
        @endif
        <hr>
        @foreach ($order->items as $item)
            <div class="item">
                <span>{{ $item['name'] }} x{{ $item['qty'] }}</span>
                <span>R$ {{ number_format($item['price'] * $item['qty'], 2, ',', '.') }}</span>
            </div>
        @endforeach
        <hr>
        <p class="total">Total: R$ {{ number_format($order->total, 2, ',', '.') }}</p>
        <div class="footer">
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
    <button onclick="window.print()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Imprimir</button>

    <script>
        window.onload = () => {
            window.print();
        };
    </script>
</body>
</html>
