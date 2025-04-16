<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Minha Loja - {{ config('app.name', 'MenuziApp') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="{{ asset('images/MenuziAppLogo.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            max-width: 1000px;
            width: 100%;
        }

        .auth-form {
            padding: 3rem 2rem;
            flex: 1 1 500px;
        }

        .auth-form h2 {
            font-weight: 700;
            color: #2563eb;
        }

        .auth-form small {
            color: #6b7280;
        }

        .auth-illustration {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            flex: 1 1 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: white;
        }

        .auth-illustration img {
            max-width: 80%;
            height: auto;
        }

        .btn-primary {
            font-weight: 600;
        }

        .btn-primary .spinner-border {
            width: 1rem;
            height: 1rem;
            display: none;
        }

        .btn-primary.loading .spinner-border {
            display: inline-block;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .auth-illustration {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">
        <div class="auth-box">
            <!-- Formulário -->
            <div class="auth-form">
                <div class=" text-center">
                    <h2 class="">Crie sua Loja Grátis</h2>
                    <p class="text-muted">Comece sua operação com o sistema de pedidos mais eficiente do mercado.</p>
                </div>

                <form method="POST" action="{{ route('register.tenant.process') }}" id="createStoreForm">
                    @csrf

                    <div class="mb-1">
                        <label class="form-label">Nome da Loja</label>
                        <input type="text" name="store_name" class="form-control" placeholder="Minha Padaria" required>
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Slug (URL personalizada)</label>
                        <input type="text" name="slug" class="form-control" placeholder="ex: minha-padaria" required>
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Seu Nome</label>
                        <input type="text" name="name" class="form-control" placeholder="João da Silva" required>
                    </div>

                    <div class="mb-1">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" placeholder="email@exemplo.com" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Senha</label>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn">
                        Criar Loja e Começar
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>

            <!-- Ilustração lateral -->
            <div class="auth-illustration">
                <img src="{{ asset('images/MenuziAppLogo.png') }}" alt="Ilustração de Loja">
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('createStoreForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function () {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });
    </script>

</body>

</html>
