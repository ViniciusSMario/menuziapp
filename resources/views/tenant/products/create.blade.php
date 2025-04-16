@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold mb-0">{{ isset($product) ? 'Editar Produto' : 'Novo Produto' }}</h1>
            <a href="{{ route('tenant.products.index', $tenant->slug) }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                    action="{{ isset($product)
                        ? route('tenant.products.update', ['tenant' => $tenant->slug, 'product' => $product])
                        : route('tenant.products.store', ['tenant' => $tenant->slug]) }}">

                    @csrf
                    @isset($product)
                        @method('PUT')
                    @endisset

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Imagem do Produto</label>
                        @if (isset($product) && $product->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Imagem do Produto"
                                    class="rounded shadow-sm" style="max-height: 120px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome do Produto</label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                            class="form-control" placeholder="Ex: Pizza Calabresa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <textarea name="description" rows="3" class="form-control"
                            placeholder="Ex: Produto delicioso e feito com ingredientes frescos">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Preço</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" min="0" name="price"
                                value="{{ old('price', $product->price ?? '') }}" class="form-control"
                                placeholder="Ex: 19.90" required>
                        </div>
                    </div>

                    {{-- Preço Promocional --}}
                    <div class="mb-3" id="promotion-price-wrapper"
                        style="{{ old('on_promotion', $product->on_promotion ?? false) ? '' : 'display: none;' }}">
                        <label class="form-label fw-semibold">Preço Promocional</label>
                        <input type="number" step="0.01" min="0" name="promotion_price" class="form-control"
                            value="{{ old('promotion_price', $product->promotion_price ?? '') }}">
                    </div>

                    {{-- Checkbox: Está em promoção --}}
                    <div class="form-check form-switch ml-4 mb-4">
                        <input class="form-check-input" type="checkbox" name="on_promotion" id="on_promotion" value="1"
                            {{ old('on_promotion', $product->on_promotion ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="on_promotion">Produto em promoção</label>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Salvar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkbox = document.getElementById('on_promotion');
            console.log(checkbox.checked)
            const promoWrapper = document.getElementById('promotion-price-wrapper');

            // Mostrar ou esconder o campo com base no estado do checkbox
            function togglePromoField() {
                promoWrapper.style.display = checkbox.checked ? 'block' : 'none';
            }

            // Listener de mudança
            checkbox.addEventListener('change', togglePromoField);

            // ⚠️ Trigger ao carregar (caso esteja marcado via banco ou old input)
            togglePromoField();
        });
    </script>
@endsection
