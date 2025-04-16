@extends('layouts.guest')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #f97316, #ef4444);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            animation: fadeIn 0.8s ease-in-out;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .btn-login {
            background-color: #4f46e5;
            color: white;
            font-weight: 600;
        }

        .btn-login:hover {
            background-color: #4338ca;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="container d-flex justify-content-center align-items-center login-wrapper" style="min-height: 100vh;">
        <div class="col-md-6 col-lg-5 login-card">
            <a href="{{ route('login', $tenant->slug) }}" class="btn btn-outline-secondary mb-1 rounded-pill">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-center d-flex justify-content-center mb-4">
                <img src="{{ asset('images/MenuziApp_logo.png') }}" width="120px" alt="logo" class="rounded-3">
            </div>
            <h2 class="fw-bold text-center mb-3">Criar Conta</h2>

            <form method="POST" action="{{ route('client.register', $tenant->slug) }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nome completo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" id="name"
                               value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required autofocus placeholder="Seu nome">
                    </div>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Telefone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                        <input type="tel" name="phone" id="phone"
                               value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror"
                               required placeholder="(00) 00000-0000">
                    </div>
                    @error('phone')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirmar Senha</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control" required placeholder="••••••••">
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-login btn-lg">
                        <i class="fas fa-user-plus me-1"></i> Criar Conta
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <span class="small">Já tem uma conta?</span><br>
                <a href="{{ route('login', $tenant->slug) }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
                    <i class="fas fa-sign-in-alt me-1"></i> Fazer login
                </a>
            </div>

            <div class="text-center small text-muted mt-4">
                © {{ now()->year }} {{ config('app.name') }}. Todos os direitos reservados.
            </div>
        </div>
    </div>
@endsection
