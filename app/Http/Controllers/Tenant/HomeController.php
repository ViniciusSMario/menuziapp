<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $tenant = tenant();

        $pedidosHoje = $tenant->orders()->whereDate('created_at', today())->get();
        $totalHoje = $pedidosHoje->sum('total');
        $ticketMedioHoje = $pedidosHoje->count() > 0 ? $totalHoje / $pedidosHoje->count() : 0;
        $itensHoje = $pedidosHoje->sum(fn ($order) => collect($order->items)->sum('quantity'));

        $indicadores = [
            'pedidos_hoje' => $pedidosHoje->count(),
            'total_hoje' => $totalHoje,
            'pedidos_mes' => $tenant->orders()->whereMonth('created_at', now()->month)->count(),
            'ticket_medio_hoje' => $ticketMedioHoje,
            'itens_hoje' => $itensHoje,
        ];


        $ultimosPedidos = $tenant->orders()->latest()->take(5)->get();

        $grafico = [
            'labels' => [],
            'valores' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i)->format('d/m');
            $total = $tenant->orders()
                ->whereDate('created_at', now()->subDays($i))
                ->sum('total');
            $grafico['labels'][] = $data;
            $grafico['valores'][] = $total;
        }

        $registros = CashRegister::where('tenant_id', $tenant->id)
            ->with(['orders', 'movements']) // eager load
            ->latest()
            ->get()
            ->map(function ($caixa) {
                $vendas = $caixa->orders;
                $movimentos = $caixa->movements;

                return [
                    'id' => $caixa->id,
                    'opened_at' => $caixa->opened_at,
                    'closed_at' => $caixa->closed_at,
                    'initial_amount' => $caixa->initial_amount,
                    'total_vendas' => $vendas->sum('total'),
                    'total_pedidos' => $vendas->count(),
                    'total_dinheiro' => $vendas->where('payment_method', 'dinheiro')->sum('total'),
                    'total_cartao' => $vendas->where('payment_method', 'cartao')->sum('total'),
                    'total_pix' => $vendas->where('payment_method', 'pix')->sum('total'),
                    'suprimentos' => $movimentos->where('type', 'suprimento')->sum('amount'),
                    'sangrias' => $movimentos->where('type', 'sangria')->sum('amount'),
                    'saldo_final' =>
                    $caixa->initial_amount +
                        $vendas->sum('total') +
                        $movimentos->where('type', 'suprimento')->sum('amount') -
                        $movimentos->where('type', 'sangria')->sum('amount'),
                ];
            });

        $statusCount = $tenant->orders()
            ->whereDate('created_at', today())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $graficoStatus = [
            'dados' => [
                $statusCount['pendente'] ?? 0,
                $statusCount['aceito'] ?? 0,
                $statusCount['em_preparo'] ?? 0,
                $statusCount['finalizado'] ?? 0,
            ]
        ];

        return view('tenant.dashboard', compact('tenant', 'graficoStatus', 'registros', 'indicadores', 'ultimosPedidos', 'grafico'));
    }
}
