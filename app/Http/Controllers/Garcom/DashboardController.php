<?php

namespace App\Http\Controllers\Garcom;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Table;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $mesas = Table::with(['orders' => function ($q) {
            $q->where('status', '!=', 'finalizado');
        }])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();

        return view('garcom.dashboard', compact('mesas'));
    }

    public function comanda(Table $table)
    {
        $orders = $table->orders()->where('status', '!=', 'finalizado')->get();

        // Carrega categorias com seus adicionais
        $categories = Category::with('additionals')->where('tenant_id', auth()->user()->tenant_id)->get();

        // Carrega produtos e injeta os adicionais da sua categoria
        $products = Product::where('tenant_id', auth()->user()->tenant_id)
            ->with('category') // se ainda não tiver com eager loading
            ->get()
            ->map(function ($product) use ($categories) {
                $category = $categories->firstWhere('id', $product->category_id);
                $product->additionals = $category?->additionals ?? collect(); // importante!
                return $product;
            });

        $ultimoTelefone = optional($orders->first())->customer_phone ?? '';

        return view('garcom.comanda', compact('table', 'orders', 'products', 'categories', 'ultimoTelefone'));
    }

    public function adicionarPedido(Request $request, Table $table)
    {
        $request->validate([
            'items' => 'required|array',
            'customer_phone' => 'required|string',
        ]);
    
        // Decodifica os extras que vieram como strings JSON
        $items = collect($request->items)->map(function ($item) {
            $item['price'] = floatval($item['price']);
            $item['quantity'] = intval($item['quantity']);
            $item['observation'] = $item['observation'] ?? '';
    
            $item['extras'] = collect($item['extras'] ?? [])->map(function ($extra) {
                return is_string($extra) ? json_decode($extra, true) : $extra;
            })->toArray();
    
            return $item;
        });
    
        // Calcula total com os preços dos produtos + extras
        $total = $items->sum(function ($item) {
            $subtotal = $item['price'] * $item['quantity'];
            $subtotal += collect($item['extras'])->sum('price');
            return $subtotal;
        });
    
        // Cria o pedido
        Order::create([
            'tenant_id' => auth()->user()->tenant_id,
            'table_id' => $table->id,
            'items' => $items,
            'total' => $total,
            'status' => 'pendente',
            'payment_method' => 'pendente',
            'customer_name' => 'Mesa: ' . $table->name,
            'customer_phone' => $request->customer_phone,
        ]);
    
        return redirect()->route('garcom.mesa', $table)->with('success', 'Pedido enviado!');
    }    

    public function fecharComanda(Table $table)
    {
        $table->orders()->where('status', '!=', 'finalizado')->update(['status' => 'finalizado']);
        return redirect()->route('garcom.dashboard')->with('success', 'Comanda fechada!');
    }
}
