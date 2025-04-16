<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComandaController extends Controller
{
    public function ativasJson()
    {
        $tenantId = auth()->user()->tenant_id;

        // Retorna mesas com pedidos pendentes ou em preparo
        $mesas = Table::with(['orders' => function ($query) {
            $query->whereIn('status', ['pendente', 'aceito', 'em preparo']);
        }])
            ->where('tenant_id', $tenantId)
            ->get();

        return response()->json($mesas);
    }

    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        $mesas = Table::with(['orders' => function ($q) {
            $q->where('status', 'pendente');
        }])
            ->where('tenant_id', $tenantId)
            ->get();

        return view('tenant.comandas.index', compact('mesas'));
    }

    public function show(Table $table)
    {
        if ($table->tenant_id != Auth::user()->tenant_id) abort(403);

        $orders = $table->orders()->where('status', '!=', 'finalizado')->get();

        return view('tenant.comandas.show', compact('table', 'orders'));
    }

    public function fechar(Table $table)
    {
        try {
            $table->orders()->where('status', '!=', 'finalizado')->update([
                'status' => 'finalizado'
            ]);

            return redirect()->route('comandas.index')->with('success', 'Comanda fechada!');
        } catch (\Exception $e) {
            Log::error('Erro ao fechar comanda: ' . $e->getMessage());
            return back()->withErrors('Erro ao fechar comanda.');
        }
    }

    public function imprimir(Table $table)
    {
        $orders = $table->orders()->where('status', '!=', 'finalizado')->get();
        return view('tenant.comandas.imprimir', compact('table', 'orders'));
    }
}
