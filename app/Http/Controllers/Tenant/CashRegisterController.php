<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\CashMovement;
use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CashRegisterController extends Controller
{
    public function openForm()
    {
        return view('tenant.pdv.caixa_abrir');
    }

    public function open(Request $request, Tenant $tenant)
    {
        $request->validate([
            'initial_amount' => 'required|numeric|min:0',
        ]);

        try {
            $caixa = CashRegister::create([
                'tenant_id' => Auth::user()->tenant_id,
                'user_id' => Auth::id(),
                'initial_amount' => $request->initial_amount,
                'opened_at' => now(),
                'is_open' => true,
            ]);

            // Atualiza pedidos sem caixa para vincular ao novo caixa aberto
            Order::where('tenant_id', $tenant->id)
                ->whereNull('cash_register_id')
                ->whereDate('created_at', now())
                ->update(['cash_register_id' => $caixa->id]);

            return redirect()->route('tenant.pdv.index', $tenant)->with('success', 'Caixa aberto com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao abrir caixa: ' . $e->getMessage());
            return back()->withErrors('Erro ao abrir o caixa: ' .  $e->getMessage());
        }
    }

    public function close(Tenant $tenant)
    {
        try {
            $register = CashRegister::where('tenant_id', Auth::user()->tenant_id)
                ->where('is_open', true)
                ->latest()->first();

            if (!$register) return back()->withErrors('Nenhum caixa aberto.');

            $totalVendas = $register->orders()->sum('total');
            $suprimentos = $register->movements()->where('type', 'suprimento')->sum('amount');
            $sangrias = $register->movements()->where('type', 'sangria')->sum('amount');

            $finalAmount = $register->initial_amount + $totalVendas + $suprimentos - $sangrias;

            $register->update([
                'final_amount' => $finalAmount,
                'closed_at' => now(),
                'is_open' => false,
            ]);

            return redirect()->route('tenant.pdv.index', $tenant)->with('success', 'Caixa fechado com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao fechar caixa: ' . $e->getMessage());
            return back()->withErrors('Erro ao fechar o caixa: '.  $e->getMessage());
        }
    }

    public function suprimento(Request $request)
    {
        return $this->registrarMovimento($request, 'suprimento');
    }

    public function sangria(Request $request)
    {
        return $this->registrarMovimento($request, 'sangria');
    }

    protected function registrarMovimento(Request $request, $tipo)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $register = CashRegister::where('tenant_id', Auth::user()->tenant_id)
                ->where('is_open', true)
                ->latest()->first();

            if (!$register) return back()->withErrors('Nenhum caixa aberto.');

            CashMovement::create([
                'cash_register_id' => $register->id,
                'type' => $tipo,
                'amount' => $request->amount,
                'description' => $request->description,
            ]);

            return back()->with('success', ucfirst($tipo) . ' registrado com sucesso!');
        } catch (Exception $e) {
            Log::error("Erro ao registrar $tipo: " . $e->getMessage());
            return back()->withErrors("Erro ao registrar $tipo.");
        }
    }

    public function relatorio($id)
    {
        $caixa = CashRegister::with(['user', 'orders', 'movements'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        return view('tenant.pdv.caixa_relatorio', compact('caixa'));
    }

    public function historico(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = CashRegister::with('user')
            ->where('tenant_id', $tenantId)
            ->where('is_open', false)
            ->orderBy('closed_at', 'desc');

        if ($request->filled('data_inicio')) {
            $query->whereDate('closed_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('closed_at', '<=', $request->data_fim);
        }

        $caixas = $query->paginate(10);

        return view('tenant.pdv.caixa_historico', compact('caixas'));
    }
}
