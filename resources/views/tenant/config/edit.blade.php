@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold mb-0">Informações do Estabelecimento</h1>
            <a href="{{ route('tenant.dashboard', $tenant->slug) }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-7">
                {{-- FORM 1: Atualizar informações do estabelecimento --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('tenant.config.update', ['tenant' => $tenant->slug]) }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="main_color" class="form-label fw-semibold">Cor Principal do Tema</label>
                                <input type="color" name="main_color" id="main_color"
                                    class="form-control form-control-color" value="{{ $tenant->main_color ?? '#f12727' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    value="{{ old('address', $tenant->address) }}" placeholder="Ex: Rua das Flores, 123">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tempo de Entrega</label>
                                <input type="text" name="delivery_time" class="form-control" placeholder="Ex: 30-45 min"
                                    value="{{ old('delivery_time', $tenant->delivery_time) }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Horário de Funcionamento (por dia)</label>
                                <div class="border rounded p-3 bg-light-subtle">
                                    @php
                                        $weekdays = [
                                            'monday' => 'Segunda',
                                            'tuesday' => 'Terça',
                                            'wednesday' => 'Quarta',
                                            'thursday' => 'Quinta',
                                            'friday' => 'Sexta',
                                            'saturday' => 'Sábado',
                                            'sunday' => 'Domingo',
                                        ];
                                        $openHours = old(
                                            'open_hours',
                                            $tenant->open_hours ? json_decode($tenant->open_hours, true) : [],
                                        );
                                    @endphp

                                    @foreach ($weekdays as $dayKey => $dayLabel)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-md-3 fw-bold">{{ $dayLabel }}</div>
                                            <div class="col-md-3">
                                                <input type="time" name="open_hours[{{ $dayKey }}][open]"
                                                    class="form-control" value="{{ $openHours[$dayKey]['open'] ?? '' }}"
                                                    {{ isset($openHours[$dayKey]['closed']) && $openHours[$dayKey]['closed'] ? 'disabled' : '' }}>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="time" name="open_hours[{{ $dayKey }}][close]"
                                                    class="form-control" value="{{ $openHours[$dayKey]['close'] ?? '' }}"
                                                    {{ isset($openHours[$dayKey]['closed']) && $openHours[$dayKey]['closed'] ? 'disabled' : '' }}>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input toggle-closed"
                                                        id="closed-{{ $dayKey }}"
                                                        name="open_hours[{{ $dayKey }}][closed]" value="1"
                                                        {{ isset($openHours[$dayKey]['closed']) && $openHours[$dayKey]['closed'] ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="closed-{{ $dayKey }}">Fechado</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Logo do Estabelecimento</label><br>
                                @if ($tenant->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo"
                                            style="max-height: 100px;" class="rounded shadow-sm">
                                    </div>
                                @endif
                                <input type="file" name="logo" class="form-control">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Salvar Informações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-5">

                {{-- FORM 2: Gerenciar Banners --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Banners do Estabelecimento</h5>

                        <form method="POST"
                            action="{{ route('tenant.config.banner.store', ['tenant' => $tenant->slug]) }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-2">
                                <label class="form-label">Título (opcional)</label>
                                <input type="text" name="title" class="form-control" placeholder="Título do banner">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Imagem</label>
                                <input type="file" name="image" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Link (opcional)</label>
                                <input type="url" name="link" class="form-control" placeholder="https://...">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Ordem de Exibição</label>
                                <input type="number" name="order" class="form-control" placeholder="Ex: 1, 2, 3..."
                                    min="0" value="0">
                            </div>

                            <div class="d-flex align-items-center ml-4 mb-2">
                                <div class="form-check form-switch me-2">
                                    <input class="form-check-input" type="checkbox" name="main_banner" value="1"
                                        id="main_banner">
                                </div>
                                <label class="form-label mb-0" for="main_banner">Banner Principal</label>
                            </div>

                            <div class="d-flex align-items-center ml-4 mb-3">
                                <div class="form-check form-switch me-2">
                                    <input class="form-check-input" type="checkbox" name="active" value="1"
                                        id="active" checked>
                                </div>
                                <label class="form-label mb-0" for="active">Ativo</label>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill">
                                <i class="fas fa-plus me-1"></i> Adicionar Banner
                            </button>
                        </form>
                        <hr>
                        @if ($banners->count())
                            <div class="row mb-4 mt-3">
                                @foreach ($banners as $banner)
                                    <div class="col-md-12 mb-1">
                                        <div class="card shadow-sm h-100">
                                            <img src="{{ asset('storage/' . $banner->image) }}" class="card-img-top"
                                                style="max-height: 150px; object-fit: cover;">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $banner->title }}</h6>
                                                <p class="mb-0"><small>Link:</small> {{ $banner->link ?? '—' }}</p>
                                                <p class="mb-0"><small>Principal:</small>
                                                    {{ $banner->main_banner ? 'Sim' : 'Não' }}</p>
                                                <p class="mb-0"><small>Status:</small>
                                                    {{ $banner->active ? 'Ativo' : 'Inativo' }}</p>
                                                <p class="mb-0"><small>Ordem:</small> {{ $banner->order }}</p>
                                                <div class="d-flex justify-content-between mt-2">
                                                    <a href="{{ route('tenant.config.banner.edit', [$tenant->slug, $banner->id]) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>

                                                    <form method="POST"
                                                        action="{{ route('tenant.config.banner.destroy', [$tenant->slug, $banner->id]) }}"
                                                        onsubmit="return confirm('Tem certeza que deseja remover este banner?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i> Remover
                                                        </button>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para alternar campos quando marcar "Fechado" --}}
    <script>
        document.querySelectorAll('.toggle-closed').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const parent = this.closest('.row');
                const inputs = parent.querySelectorAll('input[type="time"]');
                inputs.forEach(i => i.disabled = this.checked);
            });
        });
    </script>
@endsection
