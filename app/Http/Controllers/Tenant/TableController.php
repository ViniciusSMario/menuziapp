<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('tenant.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('tenant.tables.create');
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Table::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name
        ]);

        return redirect()->route('tenant.tables.index', ['tenant' => $tenant->slug])->with('success', 'Mesa cadastrada com sucesso!');
    }

    public function edit(Tenant $tenant,Table $table)
    {
        if ($table->tenant_id != auth()->user()->tenant_id) abort(403);

        return view('tenant.tables.create', compact('table'));
    }

    public function update(Request $request, Tenant $tenant, Table $table)
    {
        if ($table->tenant_id != auth()->user()->tenant_id) abort(403);

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $table->update(['name' => $request->name]);

        return redirect()->route('tenant.tables.index', ['tenant' => $tenant->slug])->with('success', 'Mesa atualizada!');
    }

    public function destroy(Tenant $tenant, Table $table)
    {
        if ($table->tenant_id != auth()->user()->tenant_id) abort(403);

        $table->delete();
        return redirect()->route('tenant.tables.index', ['tenant' => $tenant->slug])->with('success', 'Mesa removida!');
    }
}