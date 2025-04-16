@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <h1 class="h4 fw-bold mb-4">Importar Categorias</h1>

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
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('tenant.categories.import', tenant()->slug) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Arquivo Excel (.xlsx ou .csv)</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-file-import me-1"></i> Importar Categorias
            </button>

            <button type="button" id="btnDownloadModelo" class="btn btn-outline-primary ms-2">
                <i class="fas fa-download me-1"></i> Baixar modelo
            </button>
            
            <a href="{{ route('tenant.categories.index', tenant()->slug) }}" class="btn btn-secondary ms-2">Voltar</a>
        </form>

        <div class="mt-4">
            <p class="text-muted">O arquivo deve conter apenas uma coluna com o nome <strong>Nome</strong>.</p>
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
