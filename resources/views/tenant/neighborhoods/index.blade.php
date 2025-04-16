@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h4 fw-bold">Bairros de Entrega</h1>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tenant.neighborhoods.create', ['tenant' => tenant()->slug]) }}"
                    class="btn btn-primary rounded-pill">
                    <i class="fas fa-plus-circle me-1"></i> Novo Bairro
                </a>
                <a href="{{ route('tenant.neighborhoods.import.form', tenant()->slug) }}"
                    class="btn btn-success rounded-pill">
                    <i class="fas fa-file-import me-1"></i> Importar
                </a>

                <button id="btnDownloadModelo" class="btn btn-outline-success rounded-pill">
                    <i class="fas fa-download me-1"></i> Baixar modelo de importação
                </button>
            </div>
        </div>

        {{-- Feedback de mensagens --}}
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
            <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabela de bairros --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Bairro</th>
                                <th>Frete</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($neighborhoods as $neighborhood)
                                <tr>
                                    <td>{{ $neighborhood->name }}</td>
                                    <td>R$ {{ number_format($neighborhood->shipping_cost, 2, ',', '.') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.neighborhoods.edit', ['tenant' => tenant()->slug, 'neighborhood' => $neighborhood->id]) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        <form method="POST"
                                            action="{{ route('tenant.neighborhoods.destroy', ['tenant' => tenant()->slug, 'neighborhood' => $neighborhood->id]) }}"
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
                                    <td colspan="3" class="text-center text-muted">Nenhum bairro cadastrado ainda.</td>
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
                ["Nome", "Valor do Frete"],
                ["Centro", "5.00"],
                ["Jardim das Flores", "7.50"]
            ]);

            XLSX.utils.book_append_sheet(wb, ws, "Bairros");
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
            a.download = "modelo-bairros.xlsx";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    </script>
@endsection
