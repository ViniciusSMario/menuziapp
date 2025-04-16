@extends('layouts.garcom')

@section('title', 'Comanda ' . $table->name)

@section('content')
    <div class="container py-3">
        <h4 class="text-center mb-4">🧾 Comanda: <strong>{{ $table->name }}</strong></h4>

        {{-- Pedidos atuais --}}
        <div class="mb-4">
            <h5 class="fw-bold text-secondary">Pedidos Atuais</h5>
            @if ($orders->isEmpty())
                <div class="alert alert-info">Nenhum pedido registrado ainda.</div>
            @else
                <ul class="list-group shadow-sm">
                    @foreach ($orders as $order)
                        <li class="list-group-item mb-2 rounded-3 border">
                            <div class="d-flex justify-content-between">
                                <strong>Pedido #{{ $order->id }}</strong>
                                <span class="text-success fw-bold">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                            </div>
                            <ul class="mb-0 mt-2 small">
                                @foreach ($order->items as $item)
                                    <li>{{ $item['name'] }} x{{ $item['quantity'] }}
                                        @if (!empty($item['extras']))
                                            <ul>
                                                @foreach ($item['extras'] as $extra)
                                                    <li class="text-muted">+ {{ $extra['name'] }} (R$
                                                        {{ number_format($extra['price'], 2, ',', '.') }})</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if (!empty($item['observation']))
                                            <div><small class="text-muted">Obs: {{ $item['observation'] }}</small></div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Busca e filtros --}}
        <div class="mb-3">
            <input type="text" id="searchProduct" class="form-control rounded-pill" placeholder="🔍 Buscar produto...">
        </div>
        <div class="mb-4">
            <select id="filterCategory" class="form-select rounded-pill">
                <option value="">Todas as categorias</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Produtos --}}
        <div class="row" id="productList">
            @foreach ($products as $product)
                <div class="col-6 col-md-4 mb-3 product-item" data-name="{{ strtolower($product->name) }}"
                    data-category="{{ $product->category_id }}">
                    <div class="card h-100 shadow-sm text-center">
                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                            <strong class="mb-1">{{ $product->name }}</strong>
                            <small class="text-muted">R$ {{ number_format($product->price, 2, ',', '.') }}</small>
                            <button type="button" class="btn btn-sm btn-success mt-2 w-100 add-product-btn"
                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}" data-extras='@json($product->additionals)'>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Carrinho --}}
        <h5 class="mt-4">🛒 Itens do Pedido</h5>
        <form method="POST" action="{{ route('garcom.pedido.store', $table) }}">
            @csrf
            <div class="mb-3">
                <label for="customer_phone" class="form-label fw-bold">📱 Celular do Cliente</label>
                @if ($ultimoTelefone)
                    <input type="text" name="customer_phone" id="customer_phone" class="form-control rounded-pill"
                        value="{{ $ultimoTelefone }}" readonly>
                    <div class="form-text">Celular já salvo para esta comanda.</div>
                @else
                    <input type="text" name="customer_phone" id="customer_phone" class="form-control rounded-pill"
                        value="{{ old('customer_phone') }}" required>
                @endif

            </div>

            <div id="cart-items" class="mb-3"></div>
            <button class="btn btn-success w-100 rounded-pill fw-bold">✅ Enviar Pedido</button>
        </form>

        {{-- Fechar comanda --}}
        <form method="POST" action="{{ route('garcom.mesa.fechar', $table) }}" class="mt-3">
            @csrf
            <button class="btn btn-outline-danger w-100 rounded-pill fw-bold">🔒 Fechar Comanda</button>
        </form>
    </div>
    <script>
        const cart = [];

        function renderCart() {
            const container = document.getElementById('cart-items');
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = '<div class="alert alert-secondary text-center">Nenhum item adicionado.</div>';
                return;
            }

            cart.forEach((item, i) => {
                let extrasHtml = '';
                item.extras.forEach(extra => {
                    extrasHtml += `
                        <input type="hidden" name="items[${i}][extras][]" value='${JSON.stringify(extra)}'>
                        <div class="small text-muted">+ ${extra.name} (R$ ${parseFloat(extra.price).toFixed(2).replace('.', ',')})</div>
                    `;
                });

                container.innerHTML += `
                    <div class="border p-3 rounded mb-2">
                        <input type="hidden" name="items[${i}][id]" value="${item.id}">
                        <input type="hidden" name="items[${i}][name]" value="${item.name}">
                        <input type="hidden" name="items[${i}][price]" value="${item.price}">
                        <input type="hidden" name="items[${i}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="items[${i}][observation]" value="${item.observation}">
                        <div class="fw-bold">${item.name} - R$ ${parseFloat(item.price).toFixed(2).replace('.', ',')}</div>
                        <div class="mt-1">${extrasHtml}</div>
                        <div class="mt-1">
                            <label>Qtd:</label>
                            <input type="number" value="${item.quantity}" min="1"
                                class="form-control form-control-sm"
                                onchange="updateQuantity(${i}, this.value)">
                        </div>
                        <div class="mt-2">
                            <label>Observação:</label>
                            <input type="text" value="${item.observation}" class="form-control form-control-sm"
                                oninput="updateObservation(${i}, this.value)">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 remove-item" data-index="${i}">Remover</button>
                    </div>
                `;
            });
        }

        function updateQuantity(index, value) {
            cart[index].quantity = parseInt(value);
        }

        function updateObservation(index, value) {
            cart[index].observation = value;
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-product-btn')) {
                const btn = e.target;
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                const price = parseFloat(btn.dataset.price);
                const extras = JSON.parse(btn.dataset.extras || '[]');

                if (extras.length > 0) {
                    Swal.fire({
                        title: `<strong>${name}</strong>`,
                        html: `
                            <div style="text-align:left;">
                                ${extras.map(extra => `
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="extra-${extra.id}"
                                                            name="extra" value="${extra.id}" data-name="${extra.name}" data-price="${extra.price}">
                                                        <label class="form-check-label" for="extra-${extra.id}">
                                                            ${extra.name} (+ R$ ${parseFloat(extra.price).toFixed(2).replace('.', ',')})
                                                        </label>
                                                    </div>
                                                `).join('')}
                            </div>
                            <textarea id="obs" class="form-control mt-3" rows="3" placeholder="Observação (opcional)" style="width:100%; resize:none;"></textarea>
                        `,
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false,
                        showCancelButton: true,
                        confirmButtonText: 'Adicionar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: () => {
                            const selected = [];
                            document.querySelectorAll('input[name="extra"]:checked').forEach(el => {
                                selected.push({
                                    id: el.value,
                                    name: el.dataset.name,
                                    price: parseFloat(el.dataset.price)
                                });
                            });
                            return {
                                extras: selected,
                                observation: document.getElementById('obs').value || ''
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cart.push({
                                id,
                                name,
                                price: parseFloat(price),
                                quantity: 1,
                                extras: result.value.extras,
                                observation: result.value.observation
                            });
                            renderCart();
                        }
                    });
                } else {
                    cart.push({
                        id,
                        name,
                        price: parseFloat(price),
                        quantity: 1,
                        extras: [],
                        observation: ''
                    });
                    renderCart();
                }
            }

            if (e.target.classList.contains('remove-item')) {
                const index = parseInt(e.target.dataset.index);
                cart.splice(index, 1);
                renderCart();
            }
        });

        document.getElementById('searchProduct').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                item.style.display = item.dataset.name.includes(q) ? 'block' : 'none';
            });
        });

        document.getElementById('filterCategory').addEventListener('change', function() {
            const cat = this.value;
            document.querySelectorAll('.product-item').forEach(item => {
                item.style.display = !cat || item.dataset.category === cat ? 'block' : 'none';
            });
        });

        renderCart();
    </script>

@endsection
