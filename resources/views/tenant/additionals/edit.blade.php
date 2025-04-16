@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Editar Adicional</h1>

    <form method="POST" action="{{ route('tenant.additionals.update', ['tenant' => $tenant->slug, 'additional' => $additional]) }}" class="card p-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome do Adicional</label>
            <input type="text" name="name" value="{{ old('name', $additional->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Preço</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $additional->price) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoria</label>
            <select name="category_id" class="form-control" required>
                <option value="">Selecione uma categoria</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $additional->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Atualizar Adicional</button>
    </form>
</div>
@endsection
