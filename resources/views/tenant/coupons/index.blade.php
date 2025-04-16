@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold">Cupons</h1>
            <a href="{{ route('tenant.coupons.create', ['tenant' => tenant()->slug]) }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus-circle me-1"></i> Novo Cupom
            </a>
        </div>

        {{-- Mensagens de feedback --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">{{ session('success') }}</div>
        @endif

        {{-- Tabela de cupons --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Desconto</th>
                                <th>Expiração</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coupons as $coupon)
                                <tr>
                                    <td><strong>{{ $coupon->code }}</strong></td>
                                    <td>
                                        @if ($coupon->type === 'percent')
                                            {{ $coupon->discount }}%
                                        @else
                                            R$ {{ number_format($coupon->discount, 2, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('d/m/Y') : 'Sem expiração' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.coupons.edit', ['tenant' => tenant()->slug, 'coupon' => $coupon->id]) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST"
                                            action="{{ route('tenant.coupons.destroy', ['tenant' => tenant()->slug, 'coupon' => $coupon->id]) }}"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Nenhum cupom cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
