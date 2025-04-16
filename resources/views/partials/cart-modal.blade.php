<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">

            {{-- Header --}}
            <div class="modal-header bg-main text-white py-3">
                <h5 class="modal-title fw-semibold" id="cartModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i> Adicionar ao Carrinho
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4 bg-light">

                <input type="hidden" id="selectedProductId">
                <input type="hidden" id="productQuantity" name="productQuantity">

                <h4 id="productName" class="fw-bold text-center mb-1"></h4>
                <p class="text-center text-success fw-bold fs-5">Preço: R$ <span id="productPrice"></span></p>

                <div id="halfPizzaWrapper" class="d-none">
                    <div class="text-center mb-3">
                        <div class="form-check form-switch d-inline-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="toggleHalfPizza">
                            <label class="form-check-label" for="toggleHalfPizza">Escolher meia pizza</label>
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Será cobrado o preço da pizza com maior valor
                        </p>
                    </div>
                
                    <div id="halfPizzaSection" class="mb-3 d-none">
                        <label class="fw-bold mb-2">Escolha os Sabores:</label>
                        <div class="row">
                            <div class="col-6">
                                <select id="halfFlavor1" class="form-select form-control">
                                    <option value="">1ª metade</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <select id="halfFlavor2" class="form-select form-control">
                                    <option value="">2ª metade</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>                

                {{-- Quantidade --}}
                <div class="d-flex justify-content-center align-items-center my-3">
                    <button class="btn btn-outline-danger rounded-circle" id="btn-minus"
                        style="width: 44px; height: 44px;">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span id="quantityDisplay" class="mx-4 fw-bold fs-4">1</span>
                    <button class="btn btn-outline-success rounded-circle" id="btn-plus"
                        style="width: 44px; height: 44px;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                {{-- Adicionais --}}
                <div id="containerAdicionais" class="mb-3">
                    <label class="fw-bold mb-2 d-block">Adicionais:</label>
                    <div id="extrasContainer" class="p-3 bg-white border rounded-3 shadow-sm small"></div>
                </div>

                {{-- Observação --}}
                <div class="mb-3">
                    <label for="productObservation" class="fw-bold">Observação:</label>
                    <textarea id="productObservation" class="form-control rounded-3" rows="2" placeholder="Exemplo: Sem cebola"></textarea>
                </div>

                {{-- Total --}}
                <p class="text-success fw-bold fs-5 text-center mt-4 mb-0">
                    Total: R$ <span id="totalPrice"></span>
                </p>
            </div>

            {{-- Footer --}}
            <div class="modal-footer bg-white p-3 pt-2 border-0 d-flex flex-column gap-2">
                <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success w-100 rounded-pill" id="btn-add-to-cart">
                    <i class="fas fa-check me-1"></i> Adicionar ao Carrinho
                </button>
            </div>
        </div>
    </div>
</div>
