<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ $tenant->logo ? asset('storage/' . $tenant->logo) : asset('images/MenuziAppLogo.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Área Administrativa - ' . ($tenant->name ?? 'MenuziApp'))</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .bg-main {
    background-color: var(--main-color) !important;
}
        :root {
            --main-color: {{ $tenant->main_color ?? '#f12727' }};
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('components.admin.sidebar')
        @include('components.admin.wrapper')
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- jQuery (sempre primeiro) -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.phone').mask('(99) 99999-9999');

            $(document).on('submit', 'form', function(e) {
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');

                // Já está com spinner? Deixa seguir
                if (submitBtn.prop('disabled')) return;

                const isDelete = form.hasClass('delete-form');

                const proceed = () => {
                    submitBtn.prop('disabled', true);

                    if (!submitBtn.find('.spinner-border').length) {
                        const spinner = $('<span>')
                            .addClass('spinner-border spinner-border-sm me-2')
                            .attr('role', 'status')
                            .attr('aria-hidden', 'true');
                        submitBtn.prepend(spinner);
                    }

                    // Envia o formulário de verdade
                    form[0].submit();
                };

                if (isDelete) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Tem certeza?',
                        text: "Esta ação não poderá ser desfeita.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Mostra loading com SweetAlert
                            Swal.fire({
                                title: 'Excluindo...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Mostra spinner no botão
                            submitBtn.prop('disabled', true);

                            // Altera o texto para "Excluindo..."
                            submitBtn.html(`
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Excluindo...
                            `);

                            form[0].submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Salvando...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    proceed();
                }

                return false;
            });
        });
    </script>

</body>

</html>
