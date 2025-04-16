@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h4 fw-bold">Produtos</h1>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tenant.products.create', ['tenant' => tenant()->slug]) }}"
                    class="btn btn-primary rounded-pill">
                    <i class="fas fa-plus-circle me-1"></i> Novo Produto
                </a>

                <a href="{{ route('tenant.products.import.form', tenant()->slug) }}" class="btn btn-success rounded-pill">
                    <i class="fas fa-file-import me-1"></i> Importar
                </a>

                <button id="btnDownloadModelo" class="btn btn-outline-success rounded-pill">
                    <i class="fas fa-download me-1"></i> Baixar modelo de importação
                </button>
            </div>
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

        {{-- Tabela de produtos --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Preço Promocional</th>
                                <th>Categoria</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($product->promotion_price, 2, ',', '.') ?? '-' }}</td>
                                    <td>{{ $product->category->name ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.products.edit', ['tenant' => tenant()->slug, 'product' => $product->id]) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form
                                            action="{{ route('tenant.products.destroy', ['tenant' => tenant()->slug, 'product' => $product->id]) }}"
                                            method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Nenhum produto cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnDownloadModelo').addEventListener('click', function() {
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet([
                ["Nome", "Preço", "Categoria ID", "Preço Promocional"],
                ["Coca-Cola 2L", "8.50", "1", "7.00"],
                ["X-Burguer", "15.00", "2", ""]
            ]);

            XLSX.utils.book_append_sheet(wb, ws, "Produtos");
            const wbout = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'array'
            });
            const blob = new Blob([wbout], {
                type: "application/octet-stream"
            });

            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "modelo-produtos.xlsx";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    </script>
@endsection
