@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Finalizar Assinatura</h4>
                </div>
                <div class="card-body text-center">
                    <p class="mb-4">Você será redirecionado para finalizar o pagamento com segurança via Stripe.</p>

                    <a href="https://buy.stripe.com/test_aEU17z5jGeBW9P24gg" target="_blank" class="btn btn-success btn-lg w-100 mb-3">Ir para Pagamento Seguro</a>

                    <small class="text-muted">Após o pagamento, sua conta será ativada automaticamente.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection