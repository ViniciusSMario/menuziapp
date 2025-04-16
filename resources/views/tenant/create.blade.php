@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Novo Tenant + Admin</h1>

        <form method="POST" action="{{ route('saas.tenants.store') }}">
            @csrf

            <h4>Dados da Empresa</h4>
            <div class="mb-3">
                <label>Nome da Empresa</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <hr>

            <h4>Dados do Admin da Empresa</h4>
            <div class="mb-3">
                <label>Nome do Admin</label>
                <input type="text" name="admin_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email do Admin</label>
                <input type="email" name="admin_email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Senha do Admin</label>
                <input type="password" name="admin_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Criar Tenant + Admin</button>
        </form>
    </div>
@endsection
