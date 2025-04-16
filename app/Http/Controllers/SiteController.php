<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Neighborhood;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class SiteController extends Controller
{
    public function index(Tenant $tenant)
    {
        try {
            $banners = $tenant->bannersOrdenados();

            $categories = $tenant->categories()
                ->with(['products', 'additionals'])
                ->get();
            return view('site.index', compact('tenant', 'categories', 'banners'));
        } catch (Exception $e) {
            report($e);
            return back()->withErrors('Erro ao carregar cardápio.');
        }
    }

    public function shop(Request $request, Tenant $tenant)
    {
        try {
            $search = $request->input('search');

            $query = Product::with('category');

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $products = $query->paginate(9);
            $categories = Category::all();

            return view('site.shop', compact('products', 'categories', 'tenant'));
        } catch (Exception $e) {
            report($e);
            return back()->withErrors('Erro ao buscar produtos.');
        }
    }

    public function checkout(Tenant $tenant)
    {
        try {
            $addresses = Auth::check() ? Auth::user()->addresses : [];
            $neighborhoods = Neighborhood::where('tenant_id', $tenant->id)->orderBy('name')->get();
            return view('site.checkout', compact('tenant', 'addresses', 'neighborhoods'));
        } catch (Exception $e) {
            report($e);
            return back()->withErrors('Erro ao carregar página de checkout.');
        }
    }

    public function processOrder(Request $request, Tenant $tenant)
    {
        DB::beginTransaction();
    
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'cart' => 'required|json',
                'payment_method' => 'required',
            ]);
    
            $user = auth()->user();
    
            if (!$user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email ?? $request->name . uniqid() . '@guest.com',
                    'phone' => $request->phone,
                    'password' => bcrypt('password1234'),
                    'type' => 'client',
                    'tenant_id' => $tenant->id
                ]);
    
                Auth::login($user);
            }
    
            $address = null;
            $shippingCost = 0;
    
            if ($request->delivery_type === 'delivery') {
                if ($request->delivery_address === 'saved' && $request->filled('saved_address')) {
                    $address = Address::findOrFail($request->saved_address);
                    $bairro = Neighborhood::where('name', $address->bairro)->first();
                    $shippingCost = $bairro?->shipping_cost ?? 0;
                } else {
                    $bairro = Neighborhood::findOrFail($request->bairro_id);
    
                    $address = Address::create([
                        'user_id' => $user->id,
                        'cep' => $request->cep,
                        'rua' => $request->rua,
                        'numero' => $request->numero,
                        'bairro' => $bairro->name ?? '-',
                        'cidade' => $request->cidade,
                        'estado' => $request->estado,
                    ]);
    
                    $shippingCost = $bairro->shipping_cost;
                }
            }
    
            $cart = json_decode($request->cart, true);
    
            // Validação opcional dos sabores (segurança extra)
            foreach ($cart as &$item) {
                if (isset($item['half_flavors'])) {
                    $left = \App\Models\Product::find($item['half_flavors']['left']);
                    $right = \App\Models\Product::find($item['half_flavors']['right']);
    
                    if (!$left || !$right) {
                        throw new \Exception("Sabor de pizza inválido.");
                    }
    
                    // Opcional: Atualiza nome automaticamente
                    $item['name'] = "1/2 {$left->name} + 1/2 {$right->name}";
                    $item['price'] = max($left->price, $right->price);
                }
            }
    
            $caixaAtual = CashRegister::where('tenant_id', $tenant->id)
                ->where('is_open', true)
                ->latest()
                ->first();
    
            Order::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'customer_phone' => $user->phone,
                'address_id' => $address?->id,
                'items' => $cart,
                'total' => $request->input('final_total'),
                'payment_method' => $request->payment_method,
                'status' => 'pendente',
                'troco' => $request->change_for ?? 0,
                'shipping_cost' => $shippingCost,
                'delivery_type' => $request->delivery_type,
                'coupon_id' => $request->coupon_id ?? null,
                'cash_register_id' => optional($caixaAtual)->id
            ]);
    
            DB::commit();
            return redirect()->route('checkout.success', compact('tenant'));
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('checkout.fail', $tenant)->with('error', 'Erro ao processar pedido.');
        }
    }    

    public function verificarUsuario(Tenant $tenant, $phone)
    {
        try {
            $phone = preg_replace('/\D/', '', $phone);
            $user = User::where('phone', $phone)->first();

            if (!$user) return redirect()->route('login', $tenant->slug);

            Auth::login($user);
            return redirect()->route('tenant.public.menu', $tenant->slug);
        } catch (Exception $e) {
            report($e);
            return back()->withErrors('Erro ao verificar usuário.');
        }
    }

    public function getFrete($id)
    {
        try {
            $bairro = Neighborhood::find($id);

            if (!$bairro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bairro não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'shipping_cost' => $bairro->shipping_cost,
                'bairro' => $bairro->name
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Erro ao buscar frete.'], 500);
        }
    }

    public function buscarProdutos(Request $request)
    {
        try {
            $search = $request->query('q');

            $categories = Category::with(['products' => function ($query) use ($search) {
                if ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                }
            }])->get();

            // Obs: $tenant não está disponível neste método como parâmetro
            return view('site.index', compact('categories', 'search'));
        } catch (Exception $e) {
            report($e);
            return back()->withErrors('Erro ao buscar produtos.');
        }
    }

    public function getAddress($id)
    {
        try {
            $address = Address::find($id);

            if (!$address) {
                return response()->json(['error' => 'Endereço não encontrado'], 404);
            }

            return response()->json([
                'id' => $address->id,
                'rua' => $address->rua,
                'numero' => $address->numero,
                'bairro' => $address->bairro,
                'neighborhood_id' => $address->neighborhood->id ?? null,
                'cidade' => $address->cidade,
                'estado' => $address->estado,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['error' => 'Erro ao buscar endereço.'], 500);
        }
    }

    public function checkoutSuccess(Tenant $tenant)
    {
        return view('site.success', compact('tenant'));
    }

    public function checkoutFail(Tenant $tenant)
    {
        return view('site.fail', compact('tenant'));
    }

    public function meusPedidos(Request $request, Tenant $tenant)
    {
        try {
            $phone = trim($request->input('phone'));
            $phone = preg_replace('/\D/', '', $phone);

            if (!$phone && !Auth::user()) {
                return view('site.meus_pedidos', ['orders' => null, 'phone' => null, 'tenant' => $tenant]);
            }

            $user = Auth::user() ?? User::where('phone', $phone)->first();

            if (!$user) {
                return view('site.meus_pedidos', [
                    'orders' => [],
                    'phone' => $phone,
                    'tenant' => $tenant,
                    'userNotFound' => true
                ]);
            }

            $orders = Order::with('address')
                ->where('user_id', $user->id)
                ->where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('site.meus_pedidos', compact('orders', 'phone', 'tenant'));
        } catch (Exception $e) {
            report($e);
            return back()->withErrors('Erro ao buscar pedidos.');
        }
    }

    public function promocoes(Tenant $tenant)
    {
        $products = $tenant->products()
            ->where('on_promotion', true)
            ->get();

        return view('site.promotions', compact('tenant', 'products'));
    }
}
