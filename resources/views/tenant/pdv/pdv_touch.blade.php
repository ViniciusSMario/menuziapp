@extends('layouts.admin')

@section('title', 'PDV Touch')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4">
            <!-- Produtos -->
            <div class="col-md-8">
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="search" class="form-control" placeholder="Digite para buscar o produto...">
                </div>
                <div class="row" id="product-grid">
                    @foreach ($products as $product)
                        <div class="col-6 col-md-4 col-lg-3 mb-4">
                            <button
                                class="btn bg-white border border-light shadow-sm rounded-3 p-3 w-100 h-100 text-center product-btn"
                                onclick='openExtrasModal(@json($product))'>
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.jpg') }}"
                                    class="mb-2 rounded-circle object-fit-cover shadow-sm"
                                    style="width: 80px; height: 80px;">
                                <div class="fw-bold text-dark small">{{ $product->name }}</div>
                                <div class="text-success small">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Comanda Atual -->
            <div class="col-md-4">
                <div class="bg-white border shadow-sm p-4 rounded-4">
                    <h5 class="fw-bold mb-3 text-center text-primary">
                        <i class="fas fa-shopping-cart me-2"></i> Finalização da Venda
                    </h5>
                    <form action="{{ route('tenant.pdv.checkout', ['tenant' => $tenant->slug]) }}" method="POST"
                        onsubmit="return prepareCheckout(this)">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small">Nome do Cliente</label>
                            <input type="text" name="customer_name" class="form-control form-control-sm rounded-3"
                                placeholder="Digite o nome">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Telefone</label>
                            <input type="text" id="customer_phone" name="customer_phone"
                                class="form-control form-control-sm rounded-3 phone" placeholder="(00) 00000-0000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Forma de Pagamento</label>
                            <select name="payment_method" class="form-select form-select-sm rounded-3">
                                <option value="dinheiro">Dinheiro</option>
                                <option value="cartao">Cartão</option>
                                <option value="pix">PIX</option>
                            </select>
                        </div>

                        <div id="cart-items" class="mt-3 small"></div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between fs-5">
                            <span class="fw-semibold">Total</span>
                            <strong id="cart-total">R$ 0,00</strong>
                        </div>

                        <input type="hidden" name="items" id="items-input">
                        <input type="hidden" name="delivery_type" value="retirada">
                        <input type="hidden" name="final_total" id="final-total-input">

                        <button class="btn btn-success w-100 mt-4 btn-lg rounded-3" onclick="clearCartAfterSubmit()">
                            <i class="fas fa-check-circle me-2"></i> Confirmar Pedido
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionais e Observação -->
    <div class="modal fade" id="extrasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-utensils me-2"></i> Personalizar Produto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body bg-light p-4">

                    <input type="hidden" id="extra-product-id">
                    <input type="hidden" id="extra-product-name">
                    <input type="hidden" id="extra-product-price">

                    <!-- Meia pizza -->
                    <div id="halfPizzaWrapper" class="mb-4 d-none">
                        <div class="form-check form-switch d-flex align-items-center ml-3 gap-2 mb-2">
                            <input class="form-check-input" type="checkbox" id="toggleHalfPizza">
                            <label class="form-check-label fw-bold" for="toggleHalfPizza">Selecionar meia pizza</label>
                        </div>
                        <p class="text-muted small ms-1"><i class="fas fa-info-circle me-1"></i> Será cobrado o valor da
                            metade mais cara.</p>

                        <div id="halfPizzaSection" class="row g-3 d-none mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">1ª metade:</label>
                                <select id="halfFlavor1" class="form-select shadow-sm rounded-3"></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">2ª metade:</label>
                                <select id="halfFlavor2" class="form-select shadow-sm rounded-3"></select>
                            </div>
                        </div>
                    </div>

                    <!-- Quantidade -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Quantidade:</label>
                        <input type="number" id="extra-product-quantity" class="form-control shadow-sm rounded-3"
                            value="1" min="1">
                    </div>

                    <!-- Adicionais -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Adicionais:</label>
                        <div id="extras-list" class="p-3 bg-white border rounded-3 shadow-sm small"></div>
                    </div>

                    <!-- Observações -->
                    <div class="mb-2">
                        <label class="form-label fw-bold">Observações:</label>
                        <textarea id="extra-observation" class="form-control rounded-3 shadow-sm" rows="2"
                            placeholder="Ex: sem cebola, bem passado..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-white border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary w-100 me-2 rounded-pill"
                        data-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success w-100 rounded-pill" onclick="confirmAddToCart()">
                        <i class="fas fa-check me-1"></i> Adicionar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .product-btn {
            transition: all 0.2s ease-in-out;
        }

        .product-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
        }

        .form-control,
        .form-select {
            font-size: 0.9rem;
        }

        .input-group-text {
            background-color: #f5f5f5;
            border: none;
        }

        .input-group input {
            border-left: none;
        }

        .btn-success {
            background-color: #009f4d;
            border-color: #009f4d;
        }

        .btn-success:hover {
            background-color: #007f3b;
        }

        #cart-total {
            font-size: 1.4rem;
            color: #000;
        }

        #extrasModal .modal-header {
            border-bottom: none;
        }

        #extrasModal .modal-footer {
            border-top: none;
        }

        #extrasModal .form-check-label {
            cursor: pointer;
        }

        #extras-list .form-check {
            padding: 0.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        #extras-list .form-check:last-child {
            border-bottom: none;
        }
    </style>

    <script>
        let cart = JSON.parse(localStorage.getItem('pdv_cart')) || [];
        let orderId = localStorage.getItem('pdv_order_id') || null;
        let currentProduct = null;

        if (orderId) {
            document.getElementById('order-id-input').value = orderId;
            document.getElementById('comanda-id-label').value = orderId;
        }

        renderCart();

        function openExtrasModal(product) {
            currentProduct = product;
            document.getElementById('extra-product-id').value = product.id;
            document.getElementById('extra-product-name').value = product.name;
            document.getElementById('extra-product-price').value = product.price;
            document.getElementById('extra-observation').value = '';
            document.getElementById('extra-product-quantity').value = 1;

            const extrasContainer = document.getElementById('extras-list');
            extrasContainer.innerHTML = '';
            (product.additionals || []).forEach(extra => {
                extrasContainer.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${extra.id}" data-name="${extra.name}" data-price="${extra.price}" id="extra-${extra.id}">
                    <label class="form-check-label" for="extra-${extra.id}">
                        ${extra.name} (R$ ${parseFloat(extra.price).toFixed(2).replace('.', ',')})
                    </label>
                </div>`;
            });

            $('#extrasModal').modal('show');

        }

        function confirmAddToCart() {
            const isHalf = document.getElementById("toggleHalfPizza").checked;
            let id, name, price, observation, quantity, selectedExtras = [];

            observation = document.getElementById('extra-observation').value;
            quantity = parseInt(document.getElementById('extra-product-quantity').value);

            if (isHalf) {
                const sabor1 = document.getElementById('halfFlavor1');
                const sabor2 = document.getElementById('halfFlavor2');

                if (!sabor1.value || !sabor2.value) {
                    alert("Selecione os dois sabores da pizza.");
                    return;
                }

                const nome1 = sabor1.selectedOptions[0].textContent;
                const nome2 = sabor2.selectedOptions[0].textContent;
                const price1 = parseFloat(sabor1.selectedOptions[0].dataset.price || 0);
                const price2 = parseFloat(sabor2.selectedOptions[0].dataset.price || 0);

                name = `1/2 ${nome1} + 1/2 ${nome2}`;
                price = Math.max(price1, price2);
                id = 'half-pizza-' + sabor1.value + '-' + sabor2.value;
            } else {
                id = parseInt(document.getElementById('extra-product-id').value);
                name = document.getElementById('extra-product-name').value;
                price = parseFloat(document.getElementById('extra-product-price').value);

                selectedExtras = [...document.querySelectorAll('#extras-list input:checked')].map(el => ({
                    id: parseInt(el.value),
                    name: el.dataset.name,
                    price: parseFloat(el.dataset.price)
                }));
            }

            cart.push({
                id,
                name,
                price,
                quantity,
                observation,
                extras: selectedExtras
            });
            localStorage.setItem('pdv_cart', JSON.stringify(cart));
            $('#extrasModal').modal('hide');
            renderCart();
        }


        function removeFromCart(index) {
            cart.splice(index, 1);
            localStorage.setItem('pdv_cart', JSON.stringify(cart));
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const totalEl = document.getElementById('cart-total');
            const finalTotalInput = document.getElementById('final-total-input');

            container.innerHTML = '';

            let total = 0;
            cart.forEach((item, index) => {
                let subtotal = item.price * item.quantity;
                let extrasHtml = '';
                if (item.extras && item.extras.length > 0) {
                    item.extras.forEach(extra => {
                        subtotal += parseFloat(extra.price) * item.quantity;
                        extrasHtml +=
                            `<div class='small text-muted'>➕ ${extra.name} (R$ ${extra.price.toFixed(2).replace('.', ',')})</div>`;
                    });
                }

                container.innerHTML += `
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${item.name}</strong> x${item.quantity}<br>
                            ${item.observation ? `<small class='text-warning'>📝 ${item.observation}</small><br>` : ''}
                            ${extrasHtml}
                        </div>
                        <div class="text-end">
                            <div>R$ ${subtotal.toFixed(2).replace('.', ',')}</div>
                            <button class="btn btn-sm btn-outline-danger mt-1" onclick="removeFromCart(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
                total += subtotal;
            });

            totalEl.innerText = 'R$ ' + total.toFixed(2).replace('.', ',');
            finalTotalInput.value = total.toFixed(2);
            document.getElementById('items-input').value = JSON.stringify(cart);
        }

        function prepareCheckout(form) {
            if (cart.length === 0) {
                alert('Carrinho vazio.');
                return false;
            }
            return true;
        }

        function clearCartAfterSubmit() {
            localStorage.removeItem('pdv_cart');
            localStorage.removeItem('pdv_order_id');
        }

        document.getElementById('search').addEventListener('input', function() {
            const termo = this.value.toLowerCase();
            document.querySelectorAll('.product-btn').forEach(btn => {
                const nome = btn.textContent.toLowerCase();
                btn.parentElement.style.display = nome.includes(termo) ? 'block' : 'none';
            });
        });

        let saboresPizza = []; // sabores que vêm do backend
        window.SABORES_PIZZA_URL = "{{ route('sabores_pizza', ['tenant' => $tenant->slug]) }}";

        // Carrega sabores de pizza via fetch
        fetch(window.SABORES_PIZZA_URL)
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    saboresPizza = data;
                }
            });

        // Abre modal e verifica se é pizza
        function openExtrasModal(product) {
            currentProduct = product;

            document.getElementById('extra-product-id').value = product.id;
            document.getElementById('extra-product-name').value = product.name;
            document.getElementById('extra-product-price').value = product.price;
            document.getElementById('extra-observation').value = '';
            document.getElementById('extra-product-quantity').value = 1;

            // Preenche adicionais
            const extrasContainer = document.getElementById('extras-list');
            extrasContainer.innerHTML = '';
            (product.additionals || []).forEach(extra => {
                extrasContainer.innerHTML += `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${extra.id}" data-name="${extra.name}" data-price="${extra.price}" id="extra-${extra.id}">
                <label class="form-check-label" for="extra-${extra.id}">
                    ${extra.name} (R$ ${parseFloat(extra.price).toFixed(2).replace('.', ',')})
                </label>
            </div>`;
            });

            // Verifica se é pizza
            const isPizza = product.category?.name.toLowerCase().includes('pizza') || product.category?.name.toLowerCase()
                .includes('pizzas');
            const wrapper = document.getElementById('halfPizzaWrapper');
            const toggle = document.getElementById('toggleHalfPizza');

            if (isPizza) {
                wrapper.classList.remove('d-none');
                toggle.checked = false;
                document.getElementById('halfPizzaSection').classList.add('d-none');
            } else {
                wrapper.classList.add('d-none');
                toggle.checked = false;
            }

            // Mostra modal
            $('#extrasModal').modal('show');
        }

        // Toggle meia pizza
        document.getElementById('toggleHalfPizza').addEventListener('change', function() {
            const section = document.getElementById('halfPizzaSection');
            if (this.checked) {
                section.classList.remove('d-none');

                const select1 = document.getElementById('halfFlavor1');
                const select2 = document.getElementById('halfFlavor2');
                select1.innerHTML = '';
                select2.innerHTML = '';

                saboresPizza.forEach((sabor) => {
                    const opt1 = document.createElement('option');
                    opt1.value = sabor.id;
                    opt1.dataset.price = sabor.price;
                    opt1.textContent = sabor.name;

                    const opt2 = opt1.cloneNode(true);

                    select1.appendChild(opt1);
                    select2.appendChild(opt2);
                });

                // Define automaticamente o sabor da primeira metade com o produto atual
                const match = saboresPizza.find(s => s.name === currentProduct.name);
                if (match) {
                    select1.value = match.id;
                }

            } else {
                section.classList.add('d-none');
                document.getElementById('halfFlavor1').innerHTML = '';
                document.getElementById('halfFlavor2').innerHTML = '';
            }
        });
    </script>
@endsection
