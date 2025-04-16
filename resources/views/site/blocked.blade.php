@extends('layouts.site')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="text-center">        
        <h1 class="h4 fw-bold text-danger mb-3">Estamos fora do ar no momento 😔</h1>
        
        <p class="text-muted">
            O cardápio de <strong>{{ $tenant->name }}</strong> está temporariamente indisponível.<br>
            Estamos trabalhando para resolver isso o mais rápido possível!
        </p>

        <p class="mt-4">
            <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </p>
    </div>
</div>
@endsection
