@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Novo Tenant</h1>
        <form method="POST" action="{{ route('saas.tenants.store') }}">
            @csrf
            <div class="mb-3">
                <label>Nome da Empresa</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
        </form>
    </div>
@endsection
