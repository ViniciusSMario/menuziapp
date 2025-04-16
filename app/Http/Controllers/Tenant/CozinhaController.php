<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Order;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CozinhaController extends Controller
{
    public function index(Tenant $tenant)
    {
        $caixaAberto = CashRegister::where('tenant_id', $tenant->id)
            ->where('is_open', true)
            ->latest('opened_at')
            ->first();

        return view('cozinha.index', compact('caixaAberto'));
    }

    public function json(Tenant $tenant)
    {
        $caixaAberto = CashRegister::where('tenant_id', $tenant->id)
            ->where('is_open', true)
            ->latest('opened_at')
            ->first();
    
        if (!$caixaAberto) {
            return response()->json([]);
        }
    
        $pedidos = Order::where('tenant_id', $tenant->id)
            ->where('cash_register_id', $caixaAberto->id)
            ->whereIn('status', ['aceito', 'em_preparo', 'finalizado'])
            ->whereDate('created_at', now())
            ->latest()
            ->get();
    
        $json = $pedidos->map(function ($pedido) {
            return [
                'id' => $pedido->id,
                'mesa' => $pedido->table->name ?? '---',
                'cliente' => $pedido->customer_name,
                'status' => $pedido->status,
                'created_at' => $pedido->created_at->toISOString(),
                'itens' => collect($pedido->items)->map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'quantity' => $item['quantity'] ?? $item['qty'] ?? 1,
                        'observation' => $item['observation'] ?? '',
                        'extras' => $item['extras'] ?? [],
                    ];
                }),
            ];
        });
    
        return response()->json($json);
    }
    

    public function atualizarStatus(Request $request, Tenant $tenant, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pendente,em_preparo,finalizado',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
}