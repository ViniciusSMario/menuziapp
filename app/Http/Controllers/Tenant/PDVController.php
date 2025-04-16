<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PDVController extends Controller
{
    public function index()
    {
        try {
            $tenant_id = auth()->user()->tenant_id;
            $categories = Category::where('tenant_id', $tenant_id)->get();
            $products = Product::where('tenant_id', $tenant_id)->get();
            $tables = Table::where('tenant_id', $tenant_id)->get();
            $caixaAtual = CashRegister::with(['user', 'orders', 'movements'])
                ->where('tenant_id', $tenant_id)
                ->where('is_open', true)
                ->latest()
                ->first();

            $comandasAbertas = Order::where('tenant_id', $tenant_id)
                ->whereIn('status', ['pendente', 'aceito', 'em_preparo'])
                ->orderBy('created_at', 'desc')
                ->get();

            $pedidosRecentes = Order::whereIn('status', ['pendente', 'aceito', 'em_preparo', 'finalizado'])
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->get();

            return view('tenant.pdv.index', compact('pedidosRecentes', 'categories', 'products', 'tables', 'caixaAtual', 'comandasAbertas'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar PDV: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar a tela do PDV.');
        }
    }

    public function checkout(Request $request, Tenant $tenant)
    {
        DB::beginTransaction();

        try {
            // Validação básica do request
            $request->validate([
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'items' => 'required|json',
                'payment_method' => 'required|string',
            ]);

            $user = auth()->user();

            // Decodifica os itens recebidos como JSON
            $items = json_decode($request->items, true);

            if (!is_array($items) || empty($items)) {
                throw new \Exception('Itens do pedido estão mal formatados ou vazios.');
            }

            // Sanitiza os itens do pedido
            foreach ($items as &$item) {
                $item['id'] = $item['id'] ?? null;
                $item['name'] = $item['name'] ?? 'Produto';
                $item['price'] = (float) ($item['price'] ?? 0);
                $item['quantity'] = (int) ($item['quantity'] ?? 1);
                $item['observation'] = $item['observation'] ?? null;
                $item['extras'] = $item['extras'] ?? [];

                // Garante que o campo is_half_pizza esteja presente
                $item['is_half_pizza'] = isset($item['is_half_pizza']) ? (bool) $item['is_half_pizza'] : false;
            }

            // Busca o caixa aberto, se existir
            $caixaAtual = CashRegister::where('tenant_id', $user->tenant_id)
                ->where('is_open', true)
                ->latest()
                ->first();

            // Cria o pedido
            Order::create([
                'tenant_id' => $user->tenant_id,
                'user_id' => $user->id,
                'customer_name' => $request->customer_name ?? $user->name,
                'customer_phone' => $request->customer_phone ?? $user->phone,
                'address_id' => null,
                'items' => $items,
                'total' => $request->input('final_total'),
                'payment_method' => $request->payment_method ?? 'dinheiro',
                'status' => 'aceito',
                'troco' => $request->input('change_for') ?? 0,
                'shipping_cost' => 0,
                'delivery_type' => $request->input('delivery_type') ?? 'retirada',
                'cash_register_id' => optional($caixaAtual)->id,
                'table_id' => null,
            ]);

            DB::commit();
            return redirect()->route('tenant.pdv.index', compact('tenant'))->with('success', 'Pedido adicionado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('tenant.pdv.index', compact('tenant'))->with('error', 'Erro ao processar pedido: ' . $e->getMessage());
        }
    }


    public function print(Order $order)
    {
        try {
            if ($order->tenant_id != auth()->user()->tenant_id) abort(403);
            return view('tenant.pdv.print', compact('order'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar impressão do pedido: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar impressão do pedido.');
        }
    }

    public function comandas()
    {
        try {
            $tenant_id = auth()->user()->tenant_id;
            $tables = Table::where('tenant_id', $tenant_id)->get();
            $orders = Order::where('tenant_id', $tenant_id)->where('type', 'mesa')->where('status', '!=', 'finalizado')->get();
            return view('tenant.pdv.comandas', compact('tables', 'orders'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar comandas: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar comandas.');
        }
    }

    public function finalizarPedido(Order $order)
    {
        try {
            if ($order->tenant_id != auth()->user()->tenant_id) abort(403);

            $order->update(['status' => 'finalizado']);

            return redirect()->route('tenant.pdv.print', $order->id);
        } catch (Exception $e) {
            Log::error('Erro ao finalizar pedido: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao finalizar pedido.');
        }
    }
    public function adicionarItens(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'items' => 'required|json'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            $newItems = json_decode($request->items, true);

            // Concatena os itens antigos com os novos
            $itensAtuais = is_array($order->items) ? $order->items : json_decode($order->items, true);
            $todosItens = array_merge($itensAtuais, $newItems);

            $novoTotal = collect($todosItens)->sum(function ($item) {
                $subtotal = $item['price'] * $item['qty'];
                return $subtotal;
            });

            $order->update([
                'items' => $todosItens,
                'total' => $novoTotal
            ]);

            return redirect()->route('tenant.pdv.index')->with('success', 'Itens adicionados à comanda com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar itens à comanda: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao adicionar itens à comanda.');
        }
    }

    public function pdvTouch()
    {
        $tenant_id = auth()->user()->tenant_id;

        $categories = Category::with('additionals')->where('tenant_id', auth()->user()->tenant_id)->get();

        // Carrega produtos e injeta os adicionais da sua categoria
        $products = Product::where('tenant_id', auth()->user()->tenant_id)
            ->with('category') // se ainda não tiver com eager loading
            ->orderBy('name', 'ASC')
            ->get()
            ->map(function ($product) use ($categories) {
                $category = $categories->firstWhere('id', $product->category_id);
                $product->additionals = $category?->additionals ?? collect(); // importante!
                return $product;
            });

        $tables = Table::where('tenant_id', $tenant_id)->get();
        $caixaAtual = CashRegister::with(['user', 'orders', 'movements'])
            ->where('tenant_id', $tenant_id)
            ->where('is_open', true)
            ->latest()
            ->first();

        $comandasAbertas = Order::where('status', 'pendente')
            ->whereNotNull('table_id')
            ->with('table')
            ->orderBy('id', 'desc')
            ->get();

        return view('tenant.pdv.pdv_touch', compact('products', 'categories', 'tables', 'comandasAbertas'));
    }

    public function abrirComandaBalcao(Request $request)
    {
        $tenant_id = auth()->user()->tenant_id;
        $caixaAtual = CashRegister::where('tenant_id', $tenant_id)
            ->where('is_open', true)
            ->latest()
            ->first();

        $order = Order::create([
            'status' => 'pendente',
            'delivery_type' => 'retirada',
            'user_id' => auth()->id(),
            'tenant_id' => $tenant_id,
            'customer_name' => 'Balcão',
            'customer_phone' => '19999999999',
            'table_id' => null,
            'cash_register_id' => optional($caixaAtual)->id
        ]);

        return redirect()->route('tenant.pdv.touch')->with('success', 'Comanda Balcão criada (#' . $order->id . ')');
    }

    public function pedidosRecentes(Tenant $tenant)
    {
        $caixaAberto = CashRegister::where('tenant_id', $tenant->id)
            ->where('is_open', true)
            ->latest('opened_at')
            ->first();
    
        if (!$caixaAberto) {
            return view('tenant.pdv._pedidos_recentes', ['pedidosRecentes' => collect()]);
        }
    
        $pedidosRecentes = Order::where('tenant_id', $tenant->id)
            ->where('cash_register_id', $caixaAberto->id)
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->limit(12)
            ->get();
    
        return view('tenant.pdv._pedidos_recentes', compact('pedidosRecentes'));
    }
    

    public function aceitarPedido(Tenant $tenant, Order $order)
    {
        $order->update(['status' => 'aceito']);

        $this->gerarNotaPedido($order);

        $this->enviarNotificacaoWhatsApp($order, "Seu pedido #{$order->id} foi aceito e está sendo preparado!");

        return back()->with('success', 'Pedido aceito e pronto para produção.');
    }

    public function gerarNotaPedido(Order $order)
    {
        $fileName = 'nota_pedido_' . $order->id . '.pdf';

        // Gera o PDF
        $pdf = Pdf::loadView('pdf.nota-pedido', compact('order'));
        $pdf->setPaper([0, 0, 226.77, 1000], 'portrait'); // altura 1000pt (aprox 35cm)

        // Salva o arquivo no disco 'public', na pasta 'notas'
        Storage::disk('public')->put('notas/' . $fileName, $pdf->output());

        // Salva o caminho no banco (sem o 'public/')
        $order->nota_pdf = 'notas/' . $fileName;
        $order->save();
    }

    public function regenerarNota(Tenant $tenant, Order $order)
    {
        try {
            if ($order->tenant_id != auth()->user()->tenant_id) {
                abort(403);
            }

            $pdf = Pdf::loadView('pdf.nota-pedido', ['order' => $order]);
            $pdf->setPaper([0, 0, 226.77, 1000], 'portrait'); // altura 1000pt (aprox 35cm)

            $fileName = 'notas/nota-pedido-' . $order->id . '-' . time() . '.pdf';

            Storage::disk('public')->put($fileName, $pdf->output());

            $order->update([
                'nota_pdf' => $fileName
            ]);

            return redirect()->back()->with('success', 'Nota regenerada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao regenerar nota do pedido: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao gerar a nota do pedido.');
        }
    }

    public function imprimirViaTermica(Tenant $tenant, Order $order)
    {
        try {
            if ($order->tenant_id != auth()->user()->tenant_id) {
                abort(403);
            }

            if (app()->environment('local')) {
                // 👨‍💻 Modo de simulação (ambiente local): gerar PDF com visual de cupom térmico
                $pdf = Pdf::loadView('pdf.cupom-termico', compact('order'));
                $pdf->setPaper([0, 0, 226.77, 1000], 'portrait'); // altura 1000pt (aprox 35cm)
                return $pdf->stream("cupom-pedido-{$order->id}.pdf");
            }

            $connector = new WindowsPrintConnector("sua-impressora");
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("PEDIDO #{$order->id}\n");
            $printer->setEmphasis(false);
            $printer->text("RapiDelivery\n");
            $printer->text($order->created_at->format('d/m/Y H:i') . "\n\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Cliente: {$order->customer_name}\n");
            $printer->text("Telefone: {$order->customer_phone}\n");
            $printer->text("Endereço: {$order->address}\n");
            $printer->text(str_repeat("-", 32) . "\n");

            foreach ($order->items as $item) {
                $printer->text("{$item['quantity']}x {$item['name']}\n");

                if (!empty($item['extras'])) {
                    foreach ($item['extras'] as $extra) {
                        $printer->text("  + {$extra['name']} - R$ " . number_format($extra['price'], 2, ',', '.') . "\n");
                    }
                }

                if (!empty($item['observation'])) {
                    $printer->text("  📝 {$item['observation']}\n");
                }

                $printer->feed();
            }

            $printer->setEmphasis(true);
            $printer->text("TOTAL: R$ " . number_format($order->total, 2, ',', '.') . "\n");
            $printer->setEmphasis(false);

            $printer->feed(2);
            $printer->text("Pagamento: {$order->payment_method}\n");
            $printer->feed(2);
            $printer->cut(); // 🔪 Corta o papel
            $printer->close();

            return back()->with('success', 'Pedido impresso com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao imprimir pedido: ' . $e->getMessage());
            return back()->with('error', 'Erro ao imprimir pedido.');
        }
    }

    private function enviarNotificacaoWhatsApp(Order $order, string $mensagem = null)
    {
        try {
            if (!$order->customer_phone) return;

            $phone = preg_replace('/[^0-9]/', '', $order->customer_phone); // limpa o número
            $phone = '55' . $phone; // código do Brasil

            $msg = $mensagem ?? "Olá {$order->customer_name}, seu pedido #{$order->id} agora está com status: {$order->status}.";

            $url = "https://api.callmebot.com/whatsapp.php?phone={$phone}&text=" . urlencode($msg) . "&apikey=SUA_API_KEY";

            file_get_contents($url); // Simples e direto

        } catch (\Exception $e) {
            Log::error("Erro ao enviar WhatsApp: " . $e->getMessage());
        }
    }
}
