@extends('layouts.site')

@section('title', 'Finalizar Compra')

@section('content')
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-7 mb-4">

                {{-- Mensagens de erro --}}
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

                <a href="{{ route('shop', $tenant->slug) }}" class="btn btn-outline-secondary mb-1 mt-0 rounded-pill">
                    <i class="fas fa-arrow-left"></i>
                </a>
                {{-- Formulário --}}
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header text-white text-center bg-main">
                        <h4 class="mb-0">Finalizar Compra</h4>
                    </div>
                    <div class="card-body">
                        <form id="checkout-form" action="{{ route('site.checkout.process', $tenant->slug) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="cart" id="cart-data">
                            <input type="hidden" name="shipping_cost" id="shipping-cost" value="0">
                            <input type="hidden" name="saved_address" id="input-saved-address">
                            <input type="hidden" name="delivery_address" value="saved" id="delivery-address-type">
                            <input type="hidden" name="final_total" id="final-total-input" value="0">
                            <input type="hidden" name="coupon_id" id="coupon-id-input" value="">

                            {{-- Dados Pessoais --}}
                            {{-- Dados Pessoais --}}
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="section-title mb-3"><i class="fas fa-user-circle me-2 text-main"></i> Seus
                                        Dados</h5>
                                    @guest
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nome</label>
                                            <input type="text" required name="name" id="name" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Celular</label>
                                            <input type="tel" required name="phone" id="phone" class="form-control">
                                        </div>
                                    @else
                                        <p><strong>Nome:</strong> {{ Auth::user()->name }}</p>
                                        <p><strong>Celular:</strong> {{ Auth::user()->phone ?? 'Não informado' }}</p>
                                    @endguest
                                </div>
                            </div>

                            <hr>

                            {{-- Entrega --}}
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="section-title"><i class="fas fa-truck me-2 text-main"></i> Entrega</h5>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="radio" name="delivery_type" id="retirada"
                                            value="retirada" checked>
                                        <label class="form-check-label fw-semibold" for="retirada">Retirada no Local
                                            (Grátis)</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="delivery_type" id="delivery"
                                            value="delivery">
                                        <label class="form-check-label fw-semibold" for="delivery">Delivery</label>
                                    </div>

                                    {{-- Endereço --}}
                                    <div id="address-selection" class="mt-3 d-none">
                                        @auth
                                            <div id="savedAddress">
                                                <label for="saved-address" class="form-label fw-semibold">Usar endereço
                                                    salvo:</label>
                                                <select id="saved-address" class="form-control rounded-pill">
                                                    <option value="">Selecionar um endereço</option>
                                                    @foreach ($addresses as $address)
                                                        <option value="{{ $address->id }}">
                                                            {{ $address->rua }}, {{ $address->numero }} - {{ $address->bairro }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="new-address-checkbox">
                                                    <label class="form-check-label" for="new-address-checkbox">Usar um novo
                                                        endereço</label>
                                                </div>
                                            </div>
                                        @endauth
                                    </div>

                                    <div id="delivery-info" class="mt-3 d-none">
                                        {{-- Campos de endereço dinâmicos --}}
                                        <label for="cep" class="form-label fw-semibold">CEP:</label>
                                        <input type="text" id="cep" name="cep" value="{{ old('cep') }}"
                                            class="form-control rounded-pill">

                                        <label for="rua" class="form-label fw-semibold">Rua:</label>
                                        <input type="text" id="rua" name="rua" value="{{ old('rua') }}"
                                            class="form-control rounded-pill">

                                        <label for="numero" class="form-label fw-semibold">Número:</label>
                                        <input type="text" id="numero" name="numero"
                                            class="form-control rounded-pill">

                                        <label for="bairro" class="form-label fw-semibold">Bairro:</label>
                                        <select name="bairro_id" id="bairro" class="form-control rounded-pill"
                                            required>
                                            <option value="">Selecione o bairro</option>
                                            @foreach ($neighborhoods as $neighborhood)
                                                <option value="{{ $neighborhood->id }}"
                                                    {{ old('bairro_id') == $neighborhood->id ? 'selected' : '' }}>
                                                    {{ $neighborhood->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <label for="cidade" class="form-label fw-semibold">Cidade:</label>
                                        <select name="cidade" id="cidade" class="form-control rounded-pill">
                                            <option value="São José do Rio Pardo">São José do Rio Pardo</option>
                                        </select>

                                        <label for="estado" class="form-label fw-semibold">Estado:</label>
                                        <select name="estado" id="estado" class="form-control rounded-pill">
                                            <option value="SP">São Paulo</option>
                                        </select>
                                        <p class="text-muted mt-2" id="shipping-message"></p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- Pagamento --}}
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="section-title"><i class="fas fa-wallet me-2 text-main"></i> Pagamento</h5>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="dinheiro" value="dinheiro">
                                        <label class="form-check-label fw-semibold" for="dinheiro">
                                            <i class="fas fa-money-bill-wave"></i> Dinheiro
                                        </label>
                                        <div id="troco-section" class="mt-3 d-none">
                                            <label for="change_for" class="form-label fw-semibold">Troco para
                                                quanto?</label>
                                            <input type="number" min="0" step="0.01" name="change_for"
                                                id="change_for" class="form-control rounded-pill"
                                                placeholder="Ex: 100.00">
                                        </div>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="cartao" value="cartao">
                                        <label class="form-check-label fw-semibold" for="cartao">
                                            <i class="fas fa-credit-card"></i> Cartão de Crédito/Débito
                                        </label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="pix" value="pix">
                                        <label class="form-check-label fw-semibold" for="pix">
                                            <i class="fas fa-qrcode"></i> PIX
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Cupom --}}
                            <hr>
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="section-title"><i class="fas fa-ticket-alt me-2 text-main"></i> Cupom de
                                        Desconto</h5>
                                    <div class="input-group">
                                        <input type="text" name="coupon_code" id="coupon_code" class="form-control"
                                            placeholder="Digite o cupom">
                                        <button type="button" id="apply-coupon"
                                            class="btn btn-outline-primary">Aplicar</button>
                                    </div>
                                    <p id="coupon-message" class="text-success mt-2"></p>
                                </div>
                            </div>
                            <p id="coupon-message" class="text-success"></p>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5 my-3 pt-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header text-white text-center bg-main">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group mb-3" id="checkout-items"></ul>

                        <div class="bg-light rounded-4 p-3 shadow-sm">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Itens no Carrinho:</small>
                                <small id="checkout-total-items">0</small>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Entrega:</small>
                                <small id="checkout-shipping" class="text-success">Grátis</small>
                            </div>
                            <div class="d-flex justify-content-between mb-2 d-none" id="discount-line">
                                <small class="text-muted">Desconto:</small>
                                <small class="text-danger">- R$ <span id="checkout-discount">0,00</span></small>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold">Total:</h5>
                                <h5 class="fw-bold text-success">R$ <span id="checkout-total">0,00</span></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Footer --}}
    <div class="checkout-footer-mobile">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div>
                <span class="fw-bold">Total: </span>
                <span class="fw-bold text-dark">R$ <span id="checkout-total-footer">0,00</span></span>
            </div>
            <button type="submit" id="submit-mobile-btn" class="btn btn-main py-2 fs-5 rounded-pill">
                <i class="fas fa-check-circle me-2"></i> Confirmar Pedido
            </button>
        </div>
    </div>

    <style>
        /* Botões com mais suavidade */
        .btn {
            border-radius: 50px !important;
            font-weight: 600;
            transition: all 0.3s ease-in-out;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Inputs arredondados */
        .form-control {
            border-radius: 12px;
            padding: 10px 14px;
        }

        /* Cards com mais leveza */
        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            border-bottom: none;
            padding: 1rem 1.5rem;
        }

        /* Seções separadas visualmente */
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #444;
        }

        /* Sticky Footer ajustado */
        .checkout-footer-mobile {
            position: fixed;
            bottom: 70px;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #ccc;
            z-index: 1000;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        }

        /* Cupom input */
        .input-group .form-control {
            border-radius: 30px !important;
        }

        .input-group .btn {
            border-radius: 30px !important;
        }

        /* Campo troco */
        #troco-section {
            border: 1px dashed #ccc;
            padding: 10px;
            border-radius: 12px;
            background: #f8f9fa;
        }

        @media (max-width: 768px) {
            .col-lg-5 {
                margin-top: 20px;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
                window.CART_REDIRECT_URL = "{{ route('shop', $tenant->slug) }}";
                window.SABORES_PIZZA_URL = "{{ route('sabores_pizza', $tenant->slug) }}";

                const deliveryRadio = document.querySelector("#delivery");
                const retiradaRadio = document.querySelector("#retirada");
                const addressSelection = document.querySelector("#address-selection");
                const deliveryInfo = document.querySelector("#delivery-info");
                const newAddressCheckbox = document.querySelector("#new-address-checkbox");
                const savedAddressSelect = document.querySelector("#saved-address");
                const shippingInput = document.getElementById("shipping-cost");
                const shippingMsg = document.getElementById("shipping-message");
                const checkoutTotalFooter = document.querySelector("#checkout-total-footer");
                const trocoSection = document.getElementById("troco-section");
                const dinheiroRadio = document.getElementById("dinheiro");

                const form = document.getElementById("checkout-form");
                const confirmBtn = document.getElementById("submit-mobile-btn");

                // Carrinho e cupom
                const cart = JSON.parse(localStorage.getItem("cart")) || [];
                const checkoutItems = document.querySelector("#checkout-items");
                const checkoutTotal = document.querySelector("#checkout-total");
                const cartDataInput = document.querySelector("#cart-data");
                cartDataInput.value = JSON.stringify(cart);

                let discountValue = 0;
                let discountType = null;

                const applyCouponBtn = document.getElementById("apply-coupon");
                const couponInput = document.getElementById("coupon_code");
                const couponMsg = document.getElementById("coupon-message");

                // Inicialização
                renderCart();
                toggleDeliveryFields();
                loadStoredCoupon();
                validateForm();

                // Renderiza itens do carrinho
                function renderCart() {
                    if (cart.length === 0) {
                        checkoutItems.innerHTML =
                            `<li class="list-group-item text-center text-muted">Seu carrinho está vazio.</li>`;
                    } else {
                        checkoutItems.innerHTML = cart.map(item => {
                            // Somar adicionais
                            let extrasTotal = item.extras.reduce((sum, extra) => sum + extra.price, 0);
                            let unitPrice = item.price + extrasTotal;

                            return `
                <li class="list-group-item border-0 shadow-sm mb-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${item.name} (x${item.quantity})</strong>
                            ${item.observation ? `<br><small class="text-muted">Obs: ${item.observation}</small>` : ''}
                            ${item.extras.length > 0 ? `
                                                                                                                                                <ul class="small mt-1 mb-0">
                                                                                                                                                    ${item.extras.map(extra => `<li>+ ${extra.name} (R$ ${extra.price.toFixed(2).replace('.', ',')})</li>`).join('')}
                                                                                                                                                </ul>` : ''}
                        </div>
                        <div class="text-success fw-bold text-end">
                            R$ ${(unitPrice * item.quantity).toFixed(2).replace(".", ",")}
                        </div>
                    </div>
                </li>
            `;
                        }).join('');
                    }
                    recalculateTotal();
                }

                let debounceTimer;
                let lastAddressId = null;
                let isFetching = false;

                function changeSelectedAddress() {
                    const savedAddressSelect = document.getElementById('saved-address');
                    const addressId = savedAddressSelect.value;

                    if (addressId === lastAddressId) return;
                    lastAddressId = addressId;

                    // Limpa debounce anterior
                    clearTimeout(debounceTimer);

                    debounceTimer = setTimeout(() => {
                        if (!addressId) {
                            shippingInput.value = 0;
                            shippingMsg.textContent = '';
                            document.getElementById('input-saved-address').value = "";
                            document.getElementById('delivery-address-type').value = '';
                            recalculateTotal();
                            return;
                        }

                        if (isFetching) return;

                        isFetching = true;
                        Swal.fire({
                            title: 'Calculando frete...',
                            text: 'Aguarde um momento',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });

                        fetch(`/api/get-endereco/${addressId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.neighborhood_id) {
                                    return fetch(`/api/get-frete/${data.neighborhood_id}`)
                                        .then(response => response.json())
                                        .then(res => {
                                            let frete = parseFloat(res.shipping_cost || 0);
                                            shippingInput.value = frete;
                                            shippingMsg.textContent =
                                                `Frete: R$ ${frete.toFixed(2).replace('.', ',')}`;
                                            recalculateTotal();
                                        });
                                }
                            })
                            .finally(() => {
                                isFetching = false;
                                Swal.close();
                            });
                    }, 400);
                }

                function toggleDeliveryFields() {
                    if (deliveryRadio.checked) {
                        @auth
                        addressSelection.classList.remove("d-none");
                        if (newAddressCheckbox && newAddressCheckbox.checked) {
                            deliveryInfo.classList.remove("d-none");
                        } else {
                            deliveryInfo.classList.add("d-none");
                        }
                    @else
                        deliveryInfo.classList.remove("d-none");
                    @endauth
                } else {
                    shippingInput.value = 0;
                    shippingMsg.textContent = '';

                    addressSelection.classList.add("d-none");
                    deliveryInfo.classList.add("d-none");

                    const savedSelect = document.getElementById('saved-address');

                    document.getElementById("cep").value = null;
                    document.getElementById("rua").value = null;
                    document.getElementById("numero").value = null;
                    document.getElementById("bairro").value = null;
                }
                recalculateTotal();
            }

            if (newAddressCheckbox) {
                newAddressCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        deliveryInfo.classList.remove("d-none");
                        document.getElementById('input-saved-address').value = "";
                        document.getElementById('delivery-address-type').value = 'new';
                        shippingInput.value = 0;
                        shippingMsg.textContent = '';
                        recalculateTotal();
                        changeSelectedAddress();
                    } else {
                        deliveryInfo.classList.add("d-none");

                        if (savedAddressSelect.value) {
                            document.getElementById('delivery-address-type').value = 'saved';
                            document.getElementById('input-saved-address').value = savedAddressSelect.value;
                            changeSelectedAddress();
                        } else {
                            shippingInput.value = 0;
                            shippingMsg.textContent = '';
                            document.getElementById('input-saved-address').value = "";
                            document.getElementById('delivery-address-type').value = 'new';
                            recalculateTotal();
                            changeSelectedAddress();
                        }
                    }
                });
            }

            savedAddressSelect?.addEventListener('change', function() {
                if (this.value) {
                    changeSelectedAddress();
                    if (newAddressCheckbox) {
                        document.getElementById('delivery-address-type').value = 'saved';
                        document.getElementById('input-saved-address').value = this.value;

                        newAddressCheckbox.checked = false;
                        document.getElementById("cep").value = null;
                        document.getElementById("rua").value = null;
                        document.getElementById("numero").value = null;
                        document.getElementById("bairro").value = null;
                        deliveryInfo.classList.add("d-none");
                    }
                }
                if (this.value == "") {
                    document.getElementById('delivery-address-type').value = 'saved';
                    document.getElementById('input-saved-address').value = "";
                    changeSelectedAddress();
                }
            });

            // Recalcular total
            function recalculateTotal() {
                let subtotal = 0;
                let totalItems = 0;

                cart.forEach(item => {
                    let extrasTotal = item.extras.reduce((extraSum, extra) => extraSum + extra.price, 0);
                    subtotal += (item.price + extrasTotal) * item.quantity;
                    totalItems += item.quantity;
                });

                let shipping = parseFloat(shippingInput.value || 0);
                let discount = parseFloat(discountValue || 0);

                let totalWithShipping = subtotal + shipping;

                let discountFinal = discountType === 'percent' ?
                    (totalWithShipping * (discount / 100)) :
                    discount;

                discountFinal = Math.min(discountFinal, totalWithShipping);

                let finalValue = totalWithShipping - discountFinal;
                let formattedFinal = finalValue.toFixed(2).replace('.', ',');

                // Atualiza valores no HTML
                checkoutTotal.textContent = formattedFinal;
                checkoutTotalFooter.textContent = formattedFinal;
                document.getElementById("final-total-input").value = finalValue.toFixed(2);

                // Itens
                document.getElementById("checkout-total-items").textContent = totalItems;

                // Frete
                document.getElementById("checkout-shipping").textContent =
                    shipping > 0 ? `R$ ${shipping.toFixed(2).replace('.', ',')}` : "Grátis";

                // Desconto
                if (discountFinal > 0) {
                    document.getElementById("discount-line").classList.remove('d-none');
                    document.getElementById("checkout-discount").textContent = discountFinal.toFixed(2).replace('.',
                        ',');
                } else {
                    document.getElementById("discount-line").classList.add('d-none');
                }
            }


            // Buscar frete
            document.getElementById('bairro')?.addEventListener('change', function() {
                let bairroId = this.value;
                if (!bairroId) return;
                fetch(`/api/get-frete/${bairroId}`)
                    .then(response => response.json())
                    .then(res => {
                        let frete = parseFloat(res.shipping_cost || 0);
                        shippingInput.value = frete;
                        shippingMsg.textContent = `Frete: R$ ${frete.toFixed(2).replace('.', ',')}`;
                        recalculateTotal();
                    });
            });

            // Troco quando for dinheiro
            document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (dinheiroRadio.checked) {
                        trocoSection.classList.remove("d-none");
                    } else {
                        trocoSection.classList.add("d-none");
                    }
                    validateForm();
                });
            });

            // CUPOM
            applyCouponBtn.addEventListener("click", function() {
                const code = couponInput.value.trim().toUpperCase();
                if (!code) {
                    couponMsg.textContent = "Informe um código de cupom.";
                    return;
                }

                fetch(`/{{ $tenant->slug }}/api/cupom/${code}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            discountValue = parseFloat(data.discount);
                            discountType = data.type;

                            document.getElementById("coupon-id-input").value = data.id;

                            localStorage.setItem('applied_coupon', JSON.stringify({
                                code,
                                discount: discountValue,
                                type: discountType,
                                coupon_id: data.id
                            }));

                            couponMsg.textContent = data.type === 'percent' ?
                                `Cupom aplicado: ${discountValue}% de desconto!` :
                                `Cupom aplicado: R$ ${discountValue.toFixed(2)} de desconto!`;
                        } else {
                            discountValue = 0;
                            discountType = null;
                            document.getElementById("coupon-id-input").value = "";
                            couponMsg.textContent = "Cupom inválido ou expirado.";
                            localStorage.removeItem('applied_coupon');
                        }
                        recalculateTotal();
                    });
            });

            function loadStoredCoupon() {
                const storedCoupon = localStorage.getItem('applied_coupon');
                if (storedCoupon) {
                    const coupon = JSON.parse(storedCoupon);
                    discountValue = coupon.discount;
                    discountType = coupon.type;
                    couponInput.value = coupon.code;
                    document.getElementById("coupon-id-input").value = coupon.coupon_id;

                    couponMsg.textContent = discountType === 'percent' ?
                        `Cupom aplicado: ${discountValue}% de desconto!` :
                        `Cupom aplicado: R$ ${discountValue.toFixed(2)} de desconto!`;
                    recalculateTotal();
                }
            }

            // BOTÕES: unificação principal + sticky footer
            async function handleSubmit() {
                const formData = new FormData(form);
                confirmBtn.disabled = true;

                Swal.fire({
                    title: 'Enviando pedido...',
                    text: 'Aguarde um momento',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        }
                    });

                    if (response.redirected) {
                        // Se o backend retornar um redirect, segue para a página
                        window.location.href = response.url;
                    } else {
                        const result = await response.json();

                        Swal.fire({
                            title: 'Erro!',
                            text: result.message || 'Não foi possível finalizar o pedido.',
                            icon: 'error'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Houve um problema ao enviar o pedido. Tente novamente.',
                        icon: 'error'
                    });
                } finally {
                    confirmBtn.disabled = false;
                }
            }

            document.querySelector("#checkout-form").addEventListener("submit", function(e) {
                e.preventDefault();
                if (!confirmBtn.disabled) handleSubmit();
            });

            confirmBtn.addEventListener("click", function() {
                if (!confirmBtn.disabled) handleSubmit();
            });

            // VALIDAR FORM
            function validateForm() {
                let valid = true;

                @guest
                if (!document.getElementById("name").value.trim()) valid = false;
                if (!document.getElementById("phone").value.trim()) valid = false;
            @endguest

            if (deliveryRadio.checked) {
                @auth
                if (newAddressCheckbox && newAddressCheckbox.checked) {
                    if (!document.getElementById("cep").value.trim()) valid = false;
                    if (!document.getElementById("rua").value.trim()) valid = false;
                    if (!document.getElementById("numero").value.trim()) valid = false;
                    if (!document.getElementById("bairro").value) valid = false;
                } else if (!savedAddressSelect.value) {
                    valid = false;
                }
            @else
                if (!document.getElementById("cep").value.trim()) valid = false;
                if (!document.getElementById("rua").value.trim()) valid = false;
                if (!document.getElementById("numero").value.trim()) valid = false;
                if (!document.getElementById("bairro").value) valid = false;
            @endauth
        }

        const payment = document.querySelector('input[name="payment_method"]:checked');
        if (!payment) valid = false;
        if (payment && payment.value === 'dinheiro') {
            const troco = document.getElementById("change_for");
            if (troco && !troco.value.trim()) valid = false;
        }

        confirmBtn.disabled = !valid;
        }

        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('change', validateForm);
        });

        deliveryRadio.addEventListener("change", toggleDeliveryFields);
        retiradaRadio.addEventListener("change", toggleDeliveryFields);

        function updateMobileCartCount() {
            const cart = JSON.parse(localStorage.getItem("cart")) || [];
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById("mobile-cart-count");
            badge.textContent = count > 0 ? count : '0';
        }

        updateMobileCartCount();
        });
    </script>


@endsection
