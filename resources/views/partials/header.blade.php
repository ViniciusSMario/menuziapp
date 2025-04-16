{{-- HEADER MOBILE — SOMENTE EM TELAS PEQUENAS --}}
<div class="d-block d-md-none p-3 bg-light border-bottom shadow-sm mt-0">

    <div class="d-flex align-items-center mb-2">
        <img src="{{ $tenant->logo ? asset('storage/' . $tenant->logo) : asset('images/MenuziApp_logo.png') }}" alt="Logo"
            class="rounded-circle me-3 shadow-sm" width="80" height="80" style="object-fit: cover;">
        <div>
            <h5 class="mb-0 fw-bold">{{ $tenant->name }}</h5>
            <small class="text-muted d-block">{{ $tenant->address }}</small>
        </div>
    </div>

    {{-- Status + Tipo de entrega --}}
    <div class="d-flex justify-content-between align-items-center mt-2">
        <div>
            @if ($tenant->isOpen())
                <span class="text-success fw-semibold small">Aberto agora</span>
                <div class="small text-muted mt-1">{{ $tenant->getHorarioFuncionamentoHoje() }}</div>
            @else
                <span class="text-danger small fw-semibold">Fechado no momento</span>
            @endif
        </div>

        <div>
            <span class="badge bg-light border text-muted fw-semibold px-3 py-2 small">
                Entrega e Retirada
            </span>
            <br>
            <div class="text-center">
                @if ($tenant->isOpen())
                    <strong class="{{ $tenant->delivery_time ? 'text-success' : 'text-muted' }}">
                        {{ $tenant->delivery_time ?? 'Não informado' }}
                    </strong>
                @endif
            </div>
        </div>
    </div>
    @if (Auth::user())
        <div class="d-flex align-items-center justify-content-between pt-2 pb-0">
            <h5 class="fw-semibold mb-0">Olá, {{ Auth::user()->name }}!</h5>
        </div>
    @endif
</div>

{{-- HEADER DESKTOP — SOMENTE EM TELAS MÉDIAS PRA CIMA --}}
<div class="d-none d-md-block">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-start flex-wrap">

            <div class="d-flex align-items-start">
                <div class="">
                    <h2 class="fw-bold mb-0">{{ $tenant->name }}</h2>
                    <p class="text-muted mb-0">{{ $tenant->address }}</p>

                    {{-- Status e tipo --}}
                    <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                        <div class="">
                            @if ($tenant->isOpen())
                            <span class="text-success fw-semibold small badge bg-light border rounded-pill">
                                Aberto agora
                            </span>
                            <div class="small text-muted mt-1">{{ $tenant->getHorarioFuncionamentoHoje() }}</div>
                            @else
                            <span class="text-danger fw-semibold small badge bg-light border rounded-pill">
                                Fechado no Momento
                            </span>
                            @endif
                        </div>

                        <div class="text-center">
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-1 small">
                                Entrega e Retirada
                            </span>
                            <br>
                            @if ($tenant->isOpen())
                                <div class="fw-bold small mt-1 {{ $tenant->delivery_time ? 'text-success' : 'text-muted' }}">
                                    {{ $tenant->delivery_time ?? 'Não informado' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>