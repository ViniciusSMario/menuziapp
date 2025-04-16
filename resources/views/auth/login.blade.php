@extends('layouts.guest')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <div class="login-left">
        <img src="{{ asset('images/MenuziAppLogo.png') }}" alt="Logo" class="rounded-circle">
        <h1>MENUZI</h1>
        <p>Sistema de Pedidos Online</p>
    </div>

    <div class="login-right">
        <div class="login-box">
            <div class="text-center mb-4 d-flex justify-content-center">
                <img src="{{ asset('images/MenuziAppLogo.png') }}" width="100" alt="logo" class="mb-2">
            </div>
            <h2 class="text-center">Realize o Login:</h2>

            @if (session('status'))
                <div class="alert alert-success small">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login', $tenant->slug) }}">
                @csrf

                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required placeholder="E-mail ou celular">
                </div>
                @error('email')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password-field" class="form-control @error('password') is-invalid @enderror" required placeholder="Senha">
                </div>
                @error('password')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <div class="show-password">
                    <input type="checkbox" id="togglePassword" onchange="togglePasswordVisibility()">
                    <label for="togglePassword" class="form-check-label">Mostrar Senha</label>
                </div>

                <button type="submit" class="btn btn-login mb-3">Login</button>

                @if (Route::has('password.request'))
                    <div class="text-center">
                        <a href="{{ route('password.request') }}" class="text-link">Esqueceu a Senha?</a>
                    </div>
                @endif
            </form>

            <div class="footer-text">
                © {{ now()->year }} {{ config('app.name') }} – Todos os direitos reservados.
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password-field');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
@endsection
