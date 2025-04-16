<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Imports\NeighborhoodImport;
use App\Models\Neighborhood;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class NeighborhoodController extends Controller
{
    public function index()
    {
        try {
            $neighborhoods = Neighborhood::where('tenant_id', auth()->user()->tenant_id)->get();
            return view('tenant.neighborhoods.index', compact('neighborhoods'));
        } catch (Exception $e) {
            Log::error('Erro ao listar bairros: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar bairros.');
        }
    }

    public function create()
    {
        try {
            return view('tenant.neighborhoods.create');
        } catch (Exception $e) {
            Log::error('Erro ao carregar tela de criação de bairro: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar formulário de criação.');
        }
    }

    public function store(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'shipping_cost' => 'required|numeric|min:0'
        ]);

        try {
            $data['tenant_id'] = auth()->user()->tenant_id;
            Neighborhood::create($data);

            return redirect()->route('tenant.neighborhoods.index', ['tenant' => $tenant->slug])->with('success', 'Bairro criado!');
        } catch (Exception $e) {
            Log::error('Erro ao criar bairro: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao criar bairro.');
        }
    }

    public function edit(Tenant $tenant, Neighborhood $neighborhood)
    {
        try {
            if ($neighborhood->tenant_id != auth()->user()->tenant_id) abort(403);
            return view('tenant.neighborhoods.create', compact('neighborhood'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar edição do bairro: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar formulário de edição.');
        }
    }

    public function update(Request $request, Tenant $tenant, Neighborhood $neighborhood)
    {
        if ($neighborhood->tenant_id != auth()->user()->tenant_id) abort(403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'shipping_cost' => 'required|numeric|min:0'
        ]);

        try {
            $neighborhood->update($data);
            return redirect()->route('tenant.neighborhoods.index', ['tenant' => $tenant->slug])->with('success', 'Bairro atualizado!');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar bairro: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao atualizar bairro.');
        }
    }

    public function destroy(Tenant $tenant, Neighborhood $neighborhood)
    {
        try {
            if ($neighborhood->tenant_id != auth()->user()->tenant_id) abort(403);
            $neighborhood->delete();

            return redirect()->route('tenant.neighborhoods.index', ['tenant' => $tenant->slug])->with('success', 'Bairro removido!');
        } catch (Exception $e) {
            Log::error('Erro ao remover bairro: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao remover bairro.');
        }
    }

    public function importForm()
    {
        return view('tenant.neighborhoods.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new NeighborhoodImport, $request->file('file'));
            return redirect()->route('tenant.neighborhoods.index', tenant()->slug)->with('success', 'Bairros importados com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao importar bairros: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao importar bairros.');
        }
    }
}
