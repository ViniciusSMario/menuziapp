<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::where('tenant_id', auth()->user()->tenant_id)->get();
            return view('tenant.products.index', compact('products'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar produtos: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar produtos.');
        }
    }

    public function create()
    {
        try {
            $categories = \App\Models\Category::where('tenant_id', auth()->user()->tenant_id)->get();
            return view('tenant.products.create', compact('categories'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar formulário de criação de produto: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar formulário de produto.');
        }
    }

    public function store(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'promotion_price' => 'nullable|numeric|min:0|lt:price',
        ]);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['tenant_id'] = auth()->user()->tenant_id;
            $data['on_promotion'] = $request->has('on_promotion');
            $data['promotion_price'] = $request->input('promotion_price') ?? null;

            Product::create($data);

            return redirect()->route('tenant.products.index', ['tenant' => $tenant->slug])->with('success', 'Produto criado com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao criar produto: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao criar produto.');
        }
    }

    public function edit(Tenant $tenant, Product $product)
    {
        try {
            if ($product->tenant_id != auth()->user()->tenant_id) abort(403);

            $categories = \App\Models\Category::where('tenant_id', auth()->user()->tenant_id)->get();
            return view('tenant.products.create', compact('product', 'categories'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar edição de produto: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar produto para edição.');
        }
    }

    public function update(Request $request, Tenant $tenant, Product $product)
    {
        if ($product->tenant_id != auth()->user()->tenant_id) abort(403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'promotion_price' => 'nullable|numeric|min:0|lt:price',
        ]);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['on_promotion'] = $request->has('on_promotion');
            $data['promotion_price'] = $request->input('promotion_price') ?? null;

            $product->update($data);

            return redirect()->route('tenant.products.index', ['tenant' => $tenant->slug])->with('success', 'Produto atualizado!');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao atualizar produto.');
        }
    }

    public function destroy(Tenant $tenant, Product $product)
    {
        try {
            if ($product->tenant_id != auth()->user()->tenant_id) abort(403);
            $product->delete();
            return redirect()->route('tenant.products.index', ['tenant' => $tenant->slug])->with('success', 'Produto removido!');
        } catch (Exception $e) {
            Log::error('Erro ao remover produto: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao remover produto.');
        }
    }

    public function importForm()
    {
        return view('tenant.products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file'));
            return redirect()->route('tenant.products.index', tenant()->slug)->with('success', 'Produtos importados com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro na importação de produtos: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao importar produtos.');
        }
    }
}
