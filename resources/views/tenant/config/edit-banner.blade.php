@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">Editar Banner</h1>
        <a href="{{ route('tenant.config.update', $tenant->slug) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('tenant.config.banner.update', [$tenant->slug, $banner->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Imagem Atual</label><br>
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner" style="max-height: 150px;" class="rounded shadow-sm mb-2">
                    <input type="file" name="image" class="form-control mt-2">
                    <small class="text-muted">Deixe em branco para manter a imagem atual.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Título (opcional)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}" placeholder="Título do banner">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Link (opcional)</label>
                    <input type="url" name="link" class="form-control" value="{{ old('link', $banner->link) }}" placeholder="https://...">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ordem de Exibição</label>
                    <input type="number" name="order" class="form-control" min="0" value="{{ old('order', $banner->order) }}">
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="main_banner" id="main_banner" value="1"
                        {{ old('main_banner', $banner->main_banner) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="main_banner">Banner Principal</label>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
                        {{ old('active', $banner->active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="active">Ativo</label>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill">
                    <i class="fas fa-save me-1"></i> Atualizar Banner
                </button>
            </form>
        </div>
    </div>
</div>
@endsection