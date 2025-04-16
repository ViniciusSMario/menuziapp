@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h4 fw-bold mb-0">Categorias</h1>
        
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tenant.categories.create', tenant()->slug) }}" class="btn btn-primary rounded-pill">
                    <i class="fas fa-plus-circle me-1"></i> Nova Categoria
                </a>
        
                <a href="{{ route('tenant.categories.import.form', tenant()->slug) }}" class="btn btn-success rounded-pill">
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

        {{-- Tabela de categorias --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.categories.edit', ['tenant' => tenant()->slug, 'category' => $category->id]) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        <form
                                            action="{{ route('tenant.categories.destroy', ['tenant' => tenant()->slug, 'category' => $category->id]) }}"
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
                                    <td colspan="2" class="text-center text-muted">Nenhuma categoria cadastrada.</td>
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
                ["Nome"],
            ]);

            XLSX.utils.book_append_sheet(wb, ws, "Categorias");

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
            a.download = "modelo-categorias.xlsx";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    </script>
@endsection
