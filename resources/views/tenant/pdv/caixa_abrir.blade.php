@extends('layouts.admin')

@section('title', 'Abrir Caixa')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Abrir Caixa</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tenant.caixa.abrir') }}" method="POST" class="card p-4 shadow">
        @csrf
        <div class="mb-3">
            <label for="initial_amount" class="form-label">Valor Inicial em Caixa:</label>
            <input type="number" step="0.01" name="initial_amount" id="initial_amount" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Abrir Caixa</button>
    </form>
</div>
@endsection
