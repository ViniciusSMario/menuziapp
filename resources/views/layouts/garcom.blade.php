<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Garçom')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}" type="image/x-icon">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- AOS Animation CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <style>
        .header-wrapper {
            position: relative;
            height: 80px;
            /* espaço para faixa + logo */
        }

        .header-strip {
            background: var(--bs-primary);
            height: 80px;
            width: 100%;
        }

        .logo-container {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .logo-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            background: #fff;
            border: 3px solid var(--bs-yellow);
            border-radius: 50%;
        }

        .content-wrapper {
            margin-top: 70px;
        }

        body {
            padding-bottom: 70px;
            overflow-x: hidden;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">

        {{-- Header --}}
        <!-- Estrutura do header -->
        <div class="header-wrapper">
            <div class="header-strip bg-primary"></div>

            <div class="logo-container">
                <img src="{{ asset('storage/' . ($tenant->logo ?? 'default-logo.png')) }}" alt="Logo"
                    class="shadow-lg">
            </div>
        </div>

        {{-- Conteúdo Principal --}}
        <div class="content-wrapper">
            @yield('content')
        </div>


    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#phone').mask('(99) 99999-9999');
            AOS.init({
                duration: 1000,
                once: true
            });
            if (typeof window.updateCartCount === 'function') {
                window.updateCartCount();
            }
        });
    </script>
</body>

</html>
