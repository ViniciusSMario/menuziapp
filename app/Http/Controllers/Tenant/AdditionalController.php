<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Imports\AdditionalImport;
use App\Models\Additional;
use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AdditionalController extends Controller
{
    public function index()
    {
        try {
            $additionals = Additional::where('tenant_id', auth()->user()->tenant_id)
                ->with('category')
                ->get();

            return view('tenant.additionals.index', compact('additionals'));
        } catch (\Exception $e) {
            Log::error('Erro ao listar adicionais: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar os adicionais.');
        }
    }

    public function create(Tenant $tenant)
    {
        try {
            $categories = Category::where('tenant_id', auth()->user()->tenant_id)->get();
            return view('tenant.additionals.create', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de adicional: ' . $e->getMessage());
            return redirect()->route('tenant.additionals.index', ['tenant' => $tenant->slug])->with('error', 'Erro ao carregar o formulário.');
        }
    }

    public function store(Request $request, Tenant $tenant)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id'
            ]);

            $data['tenant_id'] = auth()->user()->tenant_id;
            Additional::create($data);

            return redirect()->route('tenant.additionals.index', ['tenant' => $tenant->slug])->with('success', 'Adicional criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar adicional: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao criar adicional.')->withInput();
        }
    }

    public function edit(Tenant $tenant, Additional $additional)
    {
        try {
            if ($additional->tenant_id != auth()->user()->tenant_id) abort(403);

            $categories = Category::where('tenant_id', auth()->user()->tenant_id)->get();
            return view('tenant.additionals.edit', compact('additional', 'categories'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar adicional para edição: ' . $e->getMessage());
            return redirect()->route('tenant.additionals.index', ['tenant' => $tenant->slug])->with('error', 'Erro ao carregar o adicional.');
        }
    }

    public function update(Request $request, Tenant $tenant, Additional $additional)
    {
        try {
            if ($additional->tenant_id != auth()->user()->tenant_id) abort(403);

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id'
            ]);

            $additional->update($data);

            return redirect()->route('tenant.additionals.index', ['tenant' => $tenant->slug])->with('success', 'Adicional atualizado!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar adicional: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar adicional.')->withInput();
        }
    }

    public function destroy(Tenant $tenant, Additional $additional)
    {
        try {
            if ($additional->tenant_id != auth()->user()->tenant_id) abort(403);

            $additional->delete();

            return redirect()->route('tenant.additionals.index', ['tenant' => $tenant->slug])->with('success', 'Adicional removido!');
        } catch (\Exception $e) {
            Log::error('Erro ao remover adicional: ' . $e->getMessage());
            return redirect()->route('tenant.additionals.index', ['tenant' => $tenant->slug])->with('error', 'Erro ao remover adicional.');
        }
    }

    public function importForm()
    {
        return view('tenant.additionals.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new AdditionalImport, $request->file('file'));
            return redirect()->route('tenant.additionals.index', tenant()->slug)->with('success', 'Adicionais importados com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao importar adicionais: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao importar adicionais.');
        }
    }
}
