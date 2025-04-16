<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Imports\CategoryImport;
use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::where('tenant_id', auth()->user()->tenant_id)->get();
            return view('tenant.categories.index', compact('categories'));
        } catch (Exception $e) {
            Log::error('Erro ao listar categorias: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar categorias.');
        }
    }

    public function create()
    {
        try {
            $tenant = Tenant::findOrFail(auth()->user()->tenant_id);
            return view('tenant.categories.create', compact('tenant'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar criação de categoria: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar página de criação de categoria.');
        }
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate(['name' => 'required|string|max:255']);

        try {
            Category::create([
                'tenant_id' => auth()->user()->tenant_id,
                'name' => $request->name
            ]);

            return redirect()->route('tenant.categories.index', ['tenant' => $tenant->slug])->with('success', 'Categoria criada!');
        } catch (Exception $e) {
            Log::error('Erro ao criar categoria: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao criar categoria.');
        }
    }

    public function edit(Tenant $tenant, Category $category)
    {
        try {
            if ($category->tenant_id != auth()->user()->tenant_id) abort(403);
            return view('tenant.categories.create', compact('category'));
        } catch (Exception $e) {
            Log::error('Erro ao editar categoria: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar categoria para edição.');
        }
    }

    public function update(Request $request, Tenant $tenant, Category $category)
    {
        if ($category->tenant_id != auth()->user()->tenant_id) abort(403);

        $request->validate(['name' => 'required|string|max:255']);

        try {
            $category->update(['name' => $request->name]);
            return redirect()->route('tenant.categories.index', ['tenant' => $tenant->slug])->with('success', 'Categoria atualizada!');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar categoria: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao atualizar categoria.');
        }
    }

    public function destroy(Tenant $tenant, Category $category)
    {
        try {
            if ($category->tenant_id != auth()->user()->tenant_id) abort(403);
            $category->delete();
            return redirect()->route('tenant.categories.index', ['tenant' => $tenant->slug])->with('success', 'Categoria removida!');
        } catch (Exception $e) {
            Log::error('Erro ao remover categoria: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao remover categoria.');
        }
    }

    public function importForm()
    {
        return view('tenant.categories.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new CategoryImport, $request->file('file'));
            return redirect()->route('tenant.categories.index', tenant()->slug)->with('success', 'Categorias importadas com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro na importação de categorias: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao importar categorias.');
        }
    }
}
