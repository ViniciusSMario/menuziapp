<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cardápio - ' . ($tenant->name ?? 'MenuziApp'))</title>
    <link rel="shortcut icon" href="{{ $tenant->logo ? asset('storage/' . $tenant->logo) : asset('images/MenuziAppLogo.png') }}" type="image/x-icon">

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
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <style>
        :root {
            --main-color: {{ $tenant->main_color ?? '#f12727' }};
        }

        .header-wrapper {
            position: relative;
            height: 50px;
            background: var(--main-color);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .banner-wrapper img {
            max-height: 150px;
            object-fit: cover;
            width: 100%;
            display: block;
        }

        .logo-container {
            position: absolute;
            left: 50%;
            top: -15px;
            transform: translateX(-50%);
            z-index: 20;
        }

        .logo-container img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #ffcb05;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        {{-- Banner Principal --}}
        @if ($tenant->mainBanner)
            <div class="position-relative">
                <img src="{{ asset('storage/' . $tenant->mainBanner) }}" alt="Banner"
                    class="img-fluid w-100 rounded-bottom-3"
                    style="max-height: 300px; object-fit: cover; object-position: center;">

                {{-- Logo sobreposta --}}
                <div class="position-absolute d-none d-md-block" style="bottom: -40px; left: 10%;">
                    <img src="{{ $tenant->logo ? asset('storage/' . $tenant->logo) : asset('images/MenuziAppLogo.png') }}"
                        alt="Logo" class="rounded-circle border border-white shadow"
                        style="width: 100px; height: 100px; object-fit: cover;">
                </div>
            </div>
        @else
            <div class="header-wrapper d-none d-md-block">
                <div class="logo-container d-none d-md-block"
                    style="{{ $tenant->mainBanner ? 'top: -15px' : 'top: 10px' }}">
                    <img src="{{ $tenant->logo ? asset('storage/' . $tenant->logo) : asset('images/MenuziAppLogo.png') }}"alt="Logo"
                        class="shadow-lg">
                </div>
            </div>
        @endif

        {{-- Conteúdo Principal --}}
        <div class="content-wrapper mt-2">
            @yield('content')
            <div style="margin-top: 100px">

                {{-- Sticky Bottom Navbar --}}
                @include('partials.bottom-nav')
            </div>
        </div>


    </div>

    {{-- Scripts --}}
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

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

            const swiper = new Swiper(".mySwiper", {
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });
        });
    </script>
</body>

@if (session('__previous_tenant') && session('__previous_tenant') !== session('__current_tenant'))
    <script>
        // Limpa carrinho se o tenant mudou
        localStorage.removeItem('cart');
        localStorage.removeItem('address');
        localStorage.removeItem('troco');
        localStorage.removeItem('payment_method');
        localStorage.removeItem('delivery_type');
        console.log('Carrinho limpo: mudança de tenant detectada.');
    </script>
@endif

</html>
