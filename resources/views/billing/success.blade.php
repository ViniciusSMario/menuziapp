@extends('layouts.admin')

@section('content')
<div class="container py-5 text-center">
    <h1 class="h4 mb-3 fw-bold text-success">Assinatura realizada com sucesso!</h1>
    <p class="text-muted">Obrigado por assinar. Seu sistema está ativado! 🎉</p>
    <a href="{{ route('tenant.dashboard', ['tenant' => tenant()->slug]) }}" class="btn btn-outline-primary mt-3">Ir para o Painel</a>
</div>
@endsection
