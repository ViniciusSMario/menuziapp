@extends('layouts.site')

@section('title', 'Meu Carrinho')

@section('content')
    {{-- Header com logo e status --}}
    @include('partials.header')
    <div class="container pb-4 pt-2">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 cart-container">
                <div class="card border-0 shadow rounded-4 overflow-hidden">

                    {{-- Cabeçalho --}}
                    <div class="bg-main text-white text-center py-3 px-4">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-1"></i> Meu Carrinho</h5>
                    </div>

                    {{-- Corpo --}}
                    <div class="card-body p-4">

                        {{-- Lista de Itens --}}
                        <ul class="list-group list-group-flush" id="cart-items"></ul>

                        {{-- Total --}}
                        <div class="bg-light rounded-4 p-3 mt-4 shadow-sm">

                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total:</strong>
                                <strong class="text-success fs-5">R$ <span id="cart-total">0,00</span></strong>
                            </div>
                        </div>


                        {{-- Ações --}}
                        <div class="mt-4 d-grid gap-2">
                            <button 
                                class="btn btn-main w-100 mt-3 py-3 fs-5 rounded-pill {{ !$tenant->isOpen() ? 'disabled' : '' }}" 
                                id="checkout"
                                {{ !$tenant->isOpen() ? 'disabled' : '' }}>
                                <i class="fas fa-credit-card me-2"></i> Finalizar Compra
                            </button>
                            <button class="btn btn-outline-danger rounded-pill" id="clear-cart">
                                <i class="fas fa-trash-alt me-1"></i> Limpar Carrinho
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botão Flutuante --}}
        {{-- <button class="btn btn-main btn-floating shadow-lg" id="open-cart-modal">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="cart-badge">0</span>
        </button> --}}

        {{-- Modal do Carrinho --}}
        <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel">
            <div class="modal-dialog">
                <div class="modal-content rounded-4">
                    <div class="modal-header bg-main text-white rounded-top-4">
                        <h5 class="modal-title"><i class="fas fa-shopping-basket me-1"></i> Meu Carrinho</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group list-group-flush" id="cart-modal-items"></ul>
                        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                            <span class="fw-semibold">Total:</span>
                            <span class="text-success fw-bold fs-5">R$ <span id="cart-modal-total">0,00</span></span>
                        </div>
                    </div>
                    <div class="modal-footer flex-column">
                        <button class="btn btn-outline-danger w-100 mb-2 rounded-pill" id="clear-cart-modal"><i
                                class="fas fa-trash-alt"></i> Limpar</button>
                        <button class="btn btn-success w-100 rounded-pill" id="checkout-modal" {{ !$tenant->isOpen() ? 'disabled' : '' }}><i
                            class="fas fa-credit-card"></i> Finalizar Compra</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            display: none;
        }

        .btn-floating .badge {
            font-size: 14px;
            width: 22px;
            height: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #cart-items li,
        #cart-modal-items li {
            border-radius: 8px !important;
            margin-bottom: 8px;
        }

        .list-group-item .remove-from-cart,
        .list-group-item .remove-from-cart-modal {
            cursor: pointer;
        }

        #cart-items li {
            background-color: #f9f9f9;
            padding: 12px;
            border-radius: 12px !important;
            margin-bottom: 10px;
            transition: all 0.2s ease-in-out;
        }

        #cart-items li:hover {
            background-color: #f1f1f1;
        }

        .btn-floating {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 1050;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--main-color);
            color: white;
            border: none;
        }

        .btn-floating:hover {
            background: darken(var(--main-color), 5%);
        }

        .cart-box {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
            padding: 16px;
        }

        .btn {
            transition: all 0.2s ease-in-out;
        }

        .btn:hover {
            transform: scale(1.02);
        }
    </style>

    <script> 
        document.addEventListener("DOMContentLoaded", function() {
            window.CART_REDIRECT_URL = "{{ route('shop', $tenant->slug) }}";
            window.SABORES_PIZZA_URL = "{{ route('sabores_pizza', $tenant->slug) }}";
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            // let cartBadge = document.getElementById("cart-badge");
            let cartModalItems = document.getElementById("cart-modal-items");
            let cartModalTotal = document.getElementById("cart-modal-total");

            function updateCartModalUI() {
                cartModalItems.innerHTML = "";
                let total = 0;
                let totalItems = 0;

                cart.forEach((item, index) => {
                    let adicionaisHtml = item.extras.map(extra => `
                <small class="text-muted">+ ${extra.name} (R$ ${extra.price.toFixed(2).replace(".", ",")})</small>
            `).join("<br>");

                    total += (item.price + item.extras.reduce((sum, extra) => sum + extra.price, 0)) * item
                        .quantity;
                    totalItems += item.quantity;

                    cartModalItems.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center flex-column text-start">
                    <div class="w-100">
                        <span class="fw-bold">${item.name}</span>
                        ${item.observation ? `<br><small class="text-muted">Obs: ${item.observation}</small>` : ""}
                        ${adicionaisHtml ? `<br>${adicionaisHtml}` : ""}
                    </div>
                    <div class="d-flex align-items-center w-100 justify-content-between mt-2">
                        <div class="d-flex align-items-center">
                            <span class="text-danger fw-bold decrement-qty-modal me-2" data-index="${index}" style="cursor: pointer;">−</span>
                            <span class="mx-2 fw-bold">${item.quantity}</span>
                            <span class="text-success fw-bold increment-qty-modal ms-2" data-index="${index}" style="cursor: pointer;">+</span>
                        </div>
                        <span class="fw-bold text-success">R$ ${(item.price * item.quantity).toFixed(2).replace(".", ",")}</span>
                        <span class="text-danger remove-from-cart-modal ms-3" data-index="${index}" style="cursor: pointer;"><i class="fas fa-trash"></i></span>
                    </div>
                </li>
            `;
                });

                cartModalTotal.textContent = total.toFixed(2).replace(".", ",");
                // cartBadge.textContent = totalItems;
                // cartBadge.style.display = totalItems > 0 ? "inline-block" : "none";
                localStorage.setItem("cart", JSON.stringify(cart));
            }

            function updateCartUI() {
                let cartItems = document.getElementById("cart-items");
                let cartTotal = document.getElementById("cart-total");
                cartItems.innerHTML = "";
                let total = 0;
                let totalItems = 0;

                cart.forEach((item, index) => {
                    let adicionaisHtml = item.extras.map(extra => `
                <small class="text-muted">+ ${extra.name} (R$ ${extra.price.toFixed(2).replace(".", ",")})</small>
            `).join("<br>");

                    total += (item.price + item.extras.reduce((sum, extra) => sum + extra.price, 0)) * item
                        .quantity;
                    totalItems += item.quantity;

                    cartItems.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-column text-start">
                            <div class="w-100">
                                <span class="fw-bold">${item.name}</span>
                                ${item.observation ? `<br><small class="text-muted">Obs: ${item.observation}</small>` : ""}
                                ${adicionaisHtml ? `<br>${adicionaisHtml}` : ""}
                            </div>
                            <div class="d-flex align-items-center w-100 justify-content-between mt-2">
                                <div class="d-flex align-items-center">
                                    <span class="text-danger fw-bold decrement-qty me-2" data-index="${index}" style="cursor: pointer;">−</span>
                                    <span class="mx-2 fw-bold">${item.quantity}</span>
                                    <span class="text-success fw-bold increment-qty ms-2" data-index="${index}" style="cursor: pointer;">+</span>
                                </div>
                                <span class="fw-bold text-success">R$ ${(item.price * item.quantity).toFixed(2).replace(".", ",")}</span>
                                <span class="text-danger remove-from-cart ms-3" data-index="${index}" style="cursor: pointer;"><i class="fas fa-trash"></i></span>
                            </div>
                        </li>
                    `;
                });

                cartTotal.textContent = total.toFixed(2).replace(".", ",");
                localStorage.setItem("cart", JSON.stringify(cart));
            }

            document.getElementById("cart-items").addEventListener("click", function(event) {
                let index = event.target.closest("[data-index]")?.getAttribute("data-index");

                if (event.target.closest(".increment-qty")) {
                    cart[index].quantity++;
                    updateCartUI();
                    updateCartModalUI();
                }

                if (event.target.closest(".decrement-qty")) {
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                    } else {
                        cart.splice(index, 1);
                    }
                    updateCartUI();
                    updateCartModalUI();
                }

                if (event.target.closest(".remove-from-cart")) {
                    Swal.fire({
                        title: "Tem certeza?",
                        text: "Deseja remover este item do carrinho?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Remover",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cart.splice(index, 1);
                            updateCartUI();
                            updateCartModalUI();
                            Swal.fire("Removido!", "O item foi removido.", "success");
                        }
                    });
                }
            });

            document.getElementById("checkout").addEventListener("click", function() {
                if (cart.length === 0) {
                    Swal.fire("Seu carrinho está vazio!", "Adicione produtos antes de finalizar a compra.",
                        "warning");
                    return;
                }

                localStorage.setItem("cart", JSON.stringify(cart));
                window.location.href = "{{ route('checkout', $tenant) }}";
            });

            // document.getElementById("open-cart-modal").addEventListener("click", function() {
            //     updateCartModalUI();
            //     new bootstrap.Modal(document.getElementById("cartModal")).show();
            // });

            document.getElementById("clear-cart").addEventListener("click", function() {
                if (cart.length === 0) {
                    Swal.fire("O carrinho já está vazio!", "", "info");
                    return;
                }

                Swal.fire({
                    title: "Tem certeza?",
                    text: "Deseja limpar todo o carrinho?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Limpar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        cart = [];
                        updateCartUI();
                        updateCartModalUI();
                        updateMobileCartCount();
                        Swal.fire("Carrinho esvaziado!", "", "success");
                    }
                });
            });

            function updateMobileCartCount() {
                const cart = JSON.parse(localStorage.getItem("cart")) || [];
                const count = cart.reduce((sum, item) => sum + item.quantity, 0);
                const badge = document.getElementById("mobile-cart-count");
                badge.textContent = count > 0 ? count : '0';
            }

            updateMobileCartCount();
            updateCartUI();
            updateCartModalUI();
        });
    </script>

@endsection
