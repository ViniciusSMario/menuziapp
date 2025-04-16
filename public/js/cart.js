document.addEventListener("DOMContentLoaded", () => {
    let quantity = 1;
    let saboresPizza = [];

    window.setSaboresPizza = function (sabores) {
        saboresPizza = sabores;
    };

    fetch(window.SABORES_PIZZA_URL)
        .then((res) => res.json())
        .then((data) => {
            if (Array.isArray(data)) {
                window.setSaboresPizza(data);
            }
        });

    function preencherSabores() {
        const selects = ["halfFlavor1", "halfFlavor2"];
        selects.forEach((id) => {
            const select = document.getElementById(id);
            select.innerHTML = '<option value="">Selecione</option>';
            saboresPizza.forEach((sabor) => {
                const option = document.createElement("option");
                option.value = sabor.id;
                option.dataset.price = sabor.price;
                option.textContent = sabor.name;
                select.appendChild(option);
            });
        });

        document
            .getElementById("halfFlavor1")
            .addEventListener("change", updateTotal);
        document
            .getElementById("halfFlavor2")
            .addEventListener("change", updateTotal);
    }

    $("#toggleHalfPizza").on("change", function () {
        if (this.checked) {
            $("#halfPizzaSection").removeClass("d-none");
            $("#containerAdicionais").addClass("d-none");
    
            preencherSabores();
    
            // ⏳ Aguarda o preenchimento e seta o sabor
            setTimeout(() => {
                const select1 = document.getElementById("halfFlavor1");
                for (let option of select1.options) {
                    if (
                        option.textContent.trim().toLowerCase() ===
                        window.currentSelectedFlavorName.trim().toLowerCase()
                    ) {
                        select1.value = option.value;
                        break;
                    }
                }
                updateTotal();
            }, 100);
    
            document.getElementById("halfFlavor1").value = "";
            document.getElementById("halfFlavor2").value = "";
        } else {
            $("#halfPizzaSection").addClass("d-none");
            $("#containerAdicionais").removeClass("d-none");
        }
    
        updateTotal();
    });
    

    window.openCartModal = function (
        productId,
        adicionais,
        productName,
        productPrice,
        categoryName = "",
        selectedFlavorName = ""
    ) {
        window.currentSelectedFlavorName = selectedFlavorName;

        quantity = 1;
        document.getElementById("selectedProductId").value = productId;
        document.getElementById("productName").textContent = productName;
        document.getElementById("productPrice").textContent = (
            parseFloat(productPrice) || 0
        )
            .toFixed(2)
            .replace(".", ",");
        document.getElementById("productObservation").value = "";
        document.getElementById("quantityDisplay").textContent = quantity;
        document.getElementById("productQuantity").value = quantity;

        // Reset half pizza toggle
        document.getElementById("toggleHalfPizza").checked = false;
        document.getElementById("halfFlavor1").value = "";
        document.getElementById("halfFlavor2").value = "";

        $("#halfPizzaSection").addClass("d-none");

        // Verifica se é pizza
        const isPizza =
            categoryName.toLowerCase().includes("pizza") ||
            categoryName.toLowerCase().includes("pizzas");
        const halfWrapper = document.getElementById("halfPizzaWrapper");

        if (isPizza) {
            halfWrapper.classList.remove("d-none");
        } else {
            halfWrapper.classList.add("d-none");
        }

        const extrasContainer = document.getElementById("extrasContainer");
        extrasContainer.innerHTML = "";

        const adicionaisWrapper = document.getElementById(
            "containerAdicionais"
        );

        if (adicionais.length > 0) {
            adicionaisWrapper.classList.remove("d-none");
            adicionais.forEach((extra) => {
                const precoFormatado = (parseFloat(extra.price) || 0)
                    .toFixed(2)
                    .replace(".", ",");
                const inputId = `extra-${extra.name.replace(/\s+/g, "-")}-${
                    extra.price
                }`;

                extrasContainer.innerHTML += `
                    <label for="${inputId}" class="form-check d-flex align-items-center gap-2 py-2 px-3 mb-2 rounded bg-white shadow-sm cursor-pointer w-100" style="cursor: pointer;">
                        <input id="${inputId}" class="form-check-input extra-checkbox me-2" type="checkbox"
                            value="${
                                parseFloat(extra.price) || 0
                            }" data-name="${
                    extra.name
                }" onchange="updateTotal()">
                        <span class="form-check-label w-100">
                            ${
                                extra.name
                            } <small class="text-muted">(+ R$ ${precoFormatado})</small>
                        </span>
                    </label>`;
            });
        } else {
            adicionaisWrapper.classList.add("d-none");
        }

        updateTotal();
        new bootstrap.Modal(document.getElementById("cartModal")).show();
    };

    window.updateTotal = function () {
        const isHalfPizza = document.getElementById("toggleHalfPizza").checked;
        let basePrice = 0;

        if (isHalfPizza) {
            const sabor1 = document.getElementById("halfFlavor1");
            const sabor2 = document.getElementById("halfFlavor2");
            const price1 = parseFloat(
                sabor1?.selectedOptions[0]?.dataset.price || 0
            );
            const price2 = parseFloat(
                sabor2?.selectedOptions[0]?.dataset.price || 0
            );
            basePrice = Math.max(price1, price2);
        } else {
            basePrice =
                parseFloat(
                    document
                        .getElementById("productPrice")
                        .textContent.replace(",", ".")
                ) || 0;
        }

        let extraTotal = 0;
        document
            .querySelectorAll("#extrasContainer input:checked")
            .forEach((extra) => {
                extraTotal += parseFloat(extra.value) || 0;
            });

        const total = (basePrice + extraTotal) * quantity;
        document.getElementById("totalPrice").textContent =
            total.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
    };

    document.getElementById("btn-minus").addEventListener("click", () => {
        quantity = Math.max(1, quantity - 1);
        document.getElementById("quantityDisplay").textContent = quantity;
        document.getElementById("productQuantity").value = quantity;
        updateTotal();
    });

    document.getElementById("btn-plus").addEventListener("click", () => {
        quantity++;
        document.getElementById("quantityDisplay").textContent = quantity;
        document.getElementById("productQuantity").value = quantity;
        updateTotal();
    });

    document.getElementById("btn-add-to-cart").addEventListener("click", () => {
        const isHalfPizza = document.getElementById("toggleHalfPizza").checked;
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        const productId = document.getElementById("selectedProductId").value;
        const observation = document
            .getElementById("productObservation")
            .value.trim();
        const quantity = parseInt(
            document.getElementById("productQuantity").value
        );

        let name,
            price,
            extras = [];

        if (isHalfPizza) {
            const sabor1 = document.getElementById("halfFlavor1");
            const sabor2 = document.getElementById("halfFlavor2");
            const valueSabor1 = document.getElementById("halfFlavor1").value;
            const valueSsabor2 = document.getElementById("halfFlavor2").value;

            if (!valueSabor1 || !valueSsabor2) {
                Swal.fire({
                    icon: "warning",
                    title: "Sabores obrigatórios",
                    text: "Selecione os dois sabores da pizza.",
                    confirmButtonText: "Ok",
                });
                return;
            }
            const nome1 = sabor1.selectedOptions[0].textContent;
            const nome2 = sabor2.selectedOptions[0].textContent;
            const price1 = parseFloat(
                sabor1.selectedOptions[0].dataset.price || 0
            );
            const price2 = parseFloat(
                sabor2.selectedOptions[0].dataset.price || 0
            );

            name = `1/2 ${nome1} + 1/2 ${nome2}`;
            price = Math.max(price1, price2);
        } else {
            name = document.getElementById("productName").textContent;
            price =
                parseFloat(
                    document
                        .getElementById("productPrice")
                        .textContent.replace(",", ".")
                ) || 0;

            document
                .querySelectorAll(".extra-checkbox:checked")
                .forEach((extra) => {
                    extras.push({
                        id: extra.dataset.name,
                        name: extra.dataset.name,
                        price: parseFloat(extra.value) || 0,
                    });
                });
        }

        const existingItem = cart.find(
            (item) =>
                item.id == productId &&
                item.name === name &&
                item.observation === observation &&
                JSON.stringify(item.extras) === JSON.stringify(extras)
        );

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: productId,
                name: name,
                price: price,
                quantity: quantity,
                observation: observation,
                extras: extras,
            });
        }

        localStorage.setItem("cart", JSON.stringify(cart));
        updateCartCount();
        bootstrap.Modal.getInstance(
            document.getElementById("cartModal")
        ).hide();

        Swal.fire({
            position: "center",
            icon: "success",
            title: "Produto adicionado ao carrinho!",
            confirmButtonText: "Ir para o Carrinho",
            timer: 1500,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = window.CART_REDIRECT_URL;
            }
        });
    });

    window.updateCartCount = function () {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        ["floating-cart-count", "cart-count", "mobile-cart-count"].forEach(
            (id) => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = totalItems;
                    el.style.display = totalItems > 0 ? "inline-block" : "none";
                }
            }
        );
    };

    updateCartCount();
});
