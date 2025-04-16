import Swal from 'sweetalert2';

function handleExport(selector, loadingTitle, loadingText) {
    document.querySelector(selector)?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: loadingTitle,
            text: loadingText,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        if (this.target === '_blank') {
            window.open(this.href, '_blank');
            Swal.close();
        } else {
            window.location.href = this.href;
        }
    });
}

handleExport('.export-excel', 'Gerando Excel...', 'Aguarde enquanto preparamos seu arquivo.');
handleExport('.export-pdf', 'Gerando PDF...', 'Aguarde enquanto preparamos seu arquivo.');