@extends('layouts.site')

@section('title', 'Erro no Pedido')

@section('content')
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-danger text-white rounded-top-4">
                        <h4 class="mb-0"><i class="fas fa-times-circle me-2"></i>Ocorreu um erro no pedido</h4>
                    </div>
                    <div class="card-body py-5">
                        <img src="{{ asset('images/fail.png') }}" alt="Erro no pedido" class="img-fluid mb-4"
                            style="max-width: 200px;">

                        <h5 class="text-danger fw-bold">Não foi possível finalizar seu pedido.</h5>
                        <p class="text-muted">Isso pode ter ocorrido por instabilidade na rede ou erro interno.<br>
                            Tente novamente em instantes ou entre em contato com o estabelecimento.</p>

                        <div class="mt-4 d-grid gap-2">
                            <a href="{{ route('tenant.public.menu', $tenant->slug) }}"
                                class="btn btn-outline-secondary rounded-pill">
                                <i class="fas fa-undo-alt me-1"></i> Tentar Novamente
                            </a>

                            @if ($tenant->phone)
                                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $tenant->phone) }}"
                                    target="_blank" class="btn btn-danger rounded-pill">
                                    <i class="fab fa-whatsapp me-1"></i> Falar com o Estabelecimento
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('meus_pedidos', $tenant) }}" class="text-decoration-none">
                        <i class="fas fa-list"></i> Ver meus pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
