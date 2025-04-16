<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::where('tenant_id', auth()->user()->tenant_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('tenant.orders.index', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Erro ao listar pedidos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar os pedidos.');
        }
    }

    public function show(Order $order)
    {
        try {
            if ($order->tenant_id != auth()->user()->tenant_id) abort(403);
            return view('tenant.orders.show', compact('order'));
        } catch (\Exception $e) {
            Log::error('Erro ao visualizar pedido: ' . $e->getMessage());
            return redirect()->route('tenant.orders.index')->with('error', 'Erro ao visualizar o pedido.');
        }
    }

    public function update(Order $order)
    {
        try {
            if ($order->tenant_id != auth()->user()->tenant_id) abort(403);

            $order->status = match ($order->status) {
                'pendente' => 'em_preparo',
                'em_preparo' => 'finalizado',
                default => $order->status,
            };

            $order->save();

            return redirect()->route('tenant.orders.index')->with('success', 'Status atualizado!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status do pedido: ' . $e->getMessage());
            return redirect()->route('tenant.orders.index')->with('error', 'Erro ao atualizar o status do pedido.');
        }
    }

    public function destroy(Order $order)
    {
        try {
            if ($order->tenant_id != auth()->user()->tenant_id) abort(403);
            $order->delete();

            return redirect()->route('tenant.orders.index')->with('success', 'Pedido removido!');
        } catch (\Exception $e) {
            Log::error('Erro ao remover pedido: ' . $e->getMessage());
            return redirect()->route('tenant.orders.index')->with('error', 'Erro ao remover o pedido.');
        }
    }
}