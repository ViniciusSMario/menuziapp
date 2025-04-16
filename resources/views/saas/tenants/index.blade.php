@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Tenants</h1>
        <a href="{{ route('saas.tenants.create') }}" class="btn btn-primary mb-3">Novo Tenant</a>

        @foreach($tenants as $tenant)
            <div class="card mb-2 p-3 d-flex flex-row justify-content-between">
                <div>
                    <strong>{{ $tenant->name }}</strong> - {{ $tenant->slug }}
                </div>
                <div>
                    <a href="{{ route('saas.tenants.edit', $tenant) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form method="POST" action="{{ route('saas.tenants.destroy', $tenant) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
