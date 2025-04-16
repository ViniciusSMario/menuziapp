// public/js/pdv.js
document.addEventListener("DOMContentLoaded", function() {
    function updateStatus(orderId, status) {
        fetch(`/pedidos/${orderId}/status`, {
            method: 'POST',
            body: JSON.stringify({ status }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json()).then(() => location.reload());
    }

    window.updateStatus = updateStatus;

    function imprimirPedido(orderId) {
        fetch(`/imprimir-pedido/${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Sucesso!", "Pedido enviado para a impressora!", "success");
                } else {
                    Swal.fire("Erro!", data.message, "error");
                }
            })
            .catch(error => Swal.fire("Erro!", "Não foi possível imprimir.", "error"));
    }

    window.imprimirPedido = imprimirPedido;

    // Atualização automática de pedidos
    setInterval(() => {
        fetch("/pdv/pedidos-atualizados")
            .then(res => res.text())
            .then(html => {
                document.getElementById('pedidos-wrapper').innerHTML = html;
            });
    }, 15000);
});
